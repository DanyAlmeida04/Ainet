<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http as HttpClient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Jobs\GenerateReceiptJob;
use App\Jobs\SendReceiptEmailJob;
use App\Jobs\ThrottleSendReceiptJob;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    // Mostrar carrinho (redirige para o controller existente)
    public function cart()
    {
        return app(CartController::class)->index();
    }

    // Mostrar formulário de pagamento (checkout) — apenas para utilizadores autenticados
    public function payment()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $cart = Session::get('cart', []);
        $priceConf = Price::current();
        $customer = Auth::user()->customer ?? null;

        return view('orders.payment', compact('cart', 'priceConf', 'customer'));
    }

    // Processar pagamento via API externa simulada
    public function processPayment(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'payment_type' => 'required|in:Visa,PayPal,MB WAY',
            'payment_ref' => 'required|string',
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors('O carrinho está vazio.');
        }

        // calcular total
        $priceConf = Price::current();
        $total = 0;
        foreach ($cart as $item) {
            $unit = $priceConf ? $priceConf->unit_price_catalog : 10.00;
            $total += $unit * $item['qty'];
        }

        // Enviar pedido à API simulada
        $payload = [
            'type' => $data['payment_type'],
            'reference' => $data['payment_ref'],
            'value' => (float) round($total, 2),
        ];

        $resp = HttpClient::post('https://ainet-payments-api.vercel.app/api/payments', $payload);

        if ($resp->status() == 201) {
            // Criar encomenda na BD com estado pending
            $order = Order::create([
                'status' => 'pending',
                'customer_id' => Auth::user()->id,
                'date' => Carbon::now(),
                'total_price' => $total,
                'nif' => $request->nif ?? Auth::user()->customer->nif ?? '',
                'address' => $request->address ?? Auth::user()->customer->address ?? '',
                'payment_type' => $data['payment_type'],
                'payment_ref' => $data['payment_ref'],
            ]);

            // Criar order items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'tshirt_image_id' => $item['tshirt_image_id'],
                    'color_code' => $item['color_code'],
                    'size' => $item['size'],
                    'qty' => $item['qty'],
                    'unit_price' => $priceConf->unit_price_catalog,
                    'sub_total' => $priceConf->unit_price_catalog * $item['qty'],
                ]);
            }

            // Gerar recibo em PDF e guardar em storage/app/private/pdf_receipts
            // Dispatch a job to generate the receipt synchronously (so no DB changes required)
            GenerateReceiptJob::dispatchSync($order->id);

            // Reload order to pick up updated receipt_url (if generated)
            $order->refresh();

            // Atualizar a encomenda com o caminho do recibo
            // $order already saved by job if created

            // Enviar email ao cliente com o recibo anexado
            $customer = Auth::user();
            try {
                // Send email synchronously via job (will run inline with sync driver)
                SendReceiptEmailJob::dispatchSync($order->id);
             } catch (\Throwable $e) {
                 Log::error('Erro ao enviar email do recibo: ' . $e->getMessage());
                 // não interromper o fluxo; a encomenda já foi criada
             }

            // Limpar carrinho
            Session::forget('cart');

            return redirect()->route('cart.index')->with('success', 'Pagamento aceite. Encomenda criada; o recibo será gerado e enviado por e-mail em breve.');
        }

        // Caso de erro do serviço — anexar detalhes à mensagem
        $body = $resp->body();
        return redirect()->route('cart.payment')->withErrors('Pagamento rejeitado pela plataforma externa: ' . $body)->withInput();
    }

    // List orders for authenticated customer
    public function index()
    {
        $orders = Order::where('customer_id', Auth::id())->with('items')->orderBy('date', 'desc')->paginate(12);
        return view('orders.index', compact('orders'));
    }

    // Resend receipt email
    public function resendReceipt(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $cacheKey = 'resend:' . Auth::id() . ':' . $order->id;
        $limit = 3; // allow 3 resends per hour
        $window = 3600; // seconds

        $count = Cache::get($cacheKey, 0);
        if ($count >= $limit) {
            return back()->withErrors('Limite de reenvios atingido. Tente mais tarde.');
        }

        // If receipt missing, try to generate first
        if (empty($order->receipt_url) || !file_exists(storage_path('app/' . $order->receipt_url))) {
            GenerateReceiptJob::dispatchSync($order->id);
            $order->refresh();
        }

        $receiptFullPath = $order->receipt_url ? storage_path('app/' . $order->receipt_url) : null;
        if (! $receiptFullPath || ! file_exists($receiptFullPath)) {
            return back()->withErrors('Recibo não encontrado após tentativa de geração.');
        }

        try {
            SendReceiptEmailJob::dispatchSync($order->id);
            Cache::put($cacheKey, $count + 1, $window);
            return back()->with('success', 'Recibo reenviado.');
        } catch (\Throwable $e) {
            Log::error('Erro ao reenviar recibo: ' . $e->getMessage());
            return back()->withErrors('Erro ao reenviar recibo.');
        }
    }

    // Securely view/download receipt PDF (only owner/admin)
    public function downloadReceipt(Order $order)
    {
        $this->authorize('view', $order);

        // Ensure the authenticated user owns the order
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if (empty($order->receipt_url)) {
            return redirect()->route('orders.index')->withErrors('Recibo não disponível. Por favor, tente reenviar o recibo.');
        }

        // Try a few possible locations in storage/app in case files were written to a different subfolder
        $pathsToTry = [
            storage_path('app/' . $order->receipt_url),
            storage_path('app/private/pdf_receipts/receipt_' . $order->id . '.pdf'),
            storage_path('app/public/pdf_receipts/receipt_' . $order->id . '.pdf'),
            storage_path('app/receipt_' . $order->id . '.pdf'),
        ];

        $found = null;
        foreach ($pathsToTry as $p) {
            if (file_exists($p)) {
                $found = $p;
                break;
            }
        }

        if (! $found) {
            return redirect()->route('orders.index')->withErrors('Ficheiro do recibo não encontrado. Pode reenviar o recibo.');
        }

        // If we found the file in a different place, update the saved receipt_url to the relative path under storage/app
        $relative = ltrim(str_replace(storage_path('app'), '', $found), DIRECTORY_SEPARATOR);
        if ($relative && $relative !== ltrim($order->receipt_url, '/')) {
            $order->receipt_url = $relative;
            $order->save();
        }

        // Serve the PDF inline in the browser
        $content = @file_get_contents($found);
        if ($content === false) {
            return redirect()->route('orders.index')->withErrors('Não foi possível ler o ficheiro do recibo.');
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recibo_' . $order->id . '.pdf"',
            'Content-Length' => strlen($content),
        ];

        return response($content, 200, $headers);
    }
}
