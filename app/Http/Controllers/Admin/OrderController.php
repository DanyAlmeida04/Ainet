<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use App\Jobs\GenerateReceiptJob;
use App\Jobs\SendReceiptEmailJob;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-users'); // admin only

        $q = Order::query()->with('items');

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('customer_id')) {
            $q->where('customer_id', $request->customer_id);
        }
        if ($request->filled('from')) {
            $q->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->where('date', '<=', $request->to);
        }

        $orders = $q->orderBy('date', 'desc')->paginate(20)->appends($request->query());

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        Gate::authorize('manage-users');
        $order->load('items.tshirtImage', 'customer.user');
        return view('admin.orders.show', compact('order'));
    }

    public function close(Request $request, Order $order)
    {
        Gate::authorize('manage-users');

        if ($order->status === 'closed') {
            return back()->with('info', 'Encomenda já estava fechada.');
        }

        $order->status = 'closed';
        $order->save();

        // generate receipt and send email
        try {
            GenerateReceiptJob::dispatchSync($order->id);
            SendReceiptEmailJob::dispatchSync($order->id);
        } catch (\Throwable $e) {
            Log::error('Admin close order: error generating/sending receipt: '.$e->getMessage());
        }

        return back()->with('success', 'Encomenda marcada como fechada e recibo enviado.');
    }

    public function cancel(Request $request, Order $order)
    {
        Gate::authorize('manage-users');

        $request->validate(['reason' => 'nullable|string|max:1024']);

        $order->status = 'canceled';
        $order->reason_for_cancellation = $request->reason ?? null;
        $order->save();

        // Optionally notify customer via email (not implemented Mailable here)
        Log::info('Order ' . $order->id . ' canceled by admin.');

        return back()->with('success', 'Encomenda anulada.');
    }

    public function generateAndSend(Request $request, Order $order)
    {
        Gate::authorize('manage-users');

        // Generate receipt synchronously
        GenerateReceiptJob::dispatchSync($order->id);
        $order->refresh();

        // Send email if receipt now exists
        if ($order->receipt_url) {
            try {
                SendReceiptEmailJob::dispatchSync($order->id);
            } catch (\Throwable $e) {
                Log::error('Admin generateAndSend: error sending email for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Recibo gerado e email enviado (se aplicável).');
    }

    public function preview(Order $order)
    {
        Gate::authorize('manage-users');

        // If receipt not generated yet, generate it synchronously first
        if (empty($order->receipt_url) || ! file_exists(storage_path('app/' . $order->receipt_url))) {
            GenerateReceiptJob::dispatchSync($order->id);
            $order->refresh();
        }

        // If receipt exists, stream it inline to browser
        $pathsToTry = [
            storage_path('app/' . ($order->receipt_url ?? '')),
            storage_path('app/private/pdf_receipts/receipt_' . $order->id . '.pdf'),
        ];

        foreach ($pathsToTry as $p) {
            if ($p && file_exists($p)) {
                $content = @file_get_contents($p);
                return response($content, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="recibo_' . $order->id . '.pdf"'
                ]);
            }
        }

        return back()->withErrors('Recibo não encontrado.');
    }
}
