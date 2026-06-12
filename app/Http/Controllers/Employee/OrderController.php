<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use App\Jobs\GenerateReceiptJob;
use App\Jobs\SendReceiptEmailJob;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        // allow employees and admins
        Gate::authorize('process-orders');

        $orders = Order::where('status', 'pending')->with('items.tshirtImage')->orderBy('date')->paginate(20);
        return view('employee.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        Gate::authorize('process-orders');
        $order->load('items.tshirtImage', 'customer.user');
        return view('employee.orders.show', compact('order'));
    }

    public function close(Order $order)
    {
        Gate::authorize('process-orders');

        if ($order->status === 'closed') {
            return back()->with('info', 'Encomenda já fechada.');
        }

        $order->status = 'closed';
        $order->save();

        try {
            GenerateReceiptJob::dispatchSync($order->id);
            SendReceiptEmailJob::dispatchSync($order->id);
        } catch (\Throwable $e) {
            Log::error('Employee close order: '.$e->getMessage());
        }

        return redirect()->route('employee.orders.index')->with('success', 'Encomenda processada e marcada como fechada.');
    }
}
