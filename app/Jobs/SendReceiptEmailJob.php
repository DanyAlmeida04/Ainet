<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Mail\ReceiptMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReceiptEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tries = 3;
    public array|int|null $backoff = [30, 120];

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::with('items', 'customer')->find($this->orderId);
        if (! $order) {
            Log::warning('SendReceiptEmailJob: order not found: ' . $this->orderId);
            return;
        }

        $receiptFullPath = $order->receipt_url ? storage_path('app/' . $order->receipt_url) : null;
        try {
            Mail::to($order->customer->user->email ?? $order->customer->email ?? '')->send(new ReceiptMailable($order, $receiptFullPath));
            Log::info('SendReceiptEmailJob: email sent for order ' . $order->id);
        } catch (\Throwable $e) {
            Log::error('SendReceiptEmailJob error for order ' . $order->id . ': ' . $e->getMessage());
        }
    }
}
