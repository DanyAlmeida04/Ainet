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
            $pdf = Pdf::loadView('orders.receipt', compact('order'));
            $receiptPath = 'private/pdf_receipts/receipt_' . $order->id . '.pdf';
            \Illuminate\Support\Facades\Storage::put($receiptPath, $pdf->output());

            // Atualizar a encomenda com o caminho do recibo
            $order->receipt_url = $receiptPath;
            $order->save();

            // Enviar email ao cliente com o recibo anexado
            $customer = Auth::user();
            try {
                $receiptFullPath = storage_path('app/' . $receiptPath);
                Mail::raw('Obrigado pela sua encomenda. Em anexo encontra o seu recibo.', function ($message) use ($customer, $receiptFullPath) {
                    $message->to($customer->email)
                            ->subject('Recibo da sua encomenda FunShirt');

                    if (file_exists($receiptFullPath)) {
                        $message->attach($receiptFullPath);
                    } else {
                        // fallback: não anexar se o ficheiro não existir
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Erro ao enviar email do recibo: ' . $e->getMessage());
                // não interromper o fluxo; a encomenda já foi criada
            }

            // Limpar carrinho
            Session::forget('cart');

            return redirect()->route('cart.index')->with('success', 'Pagamento aceite. Encomenda criada e recibo enviado por e-mail.');
        }

        // Caso de erro do serviço — anexar detalhes à mensagem
        $body = $resp->body();
        return redirect()->route('cart.payment')->withErrors('Pagamento rejeitado pela plataforma externa: ' . $body)->withInput();
    }

}
