<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class ThrottleSendReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::with('customer')->find($this->orderId);
        if (! $order) {
            Log::warning('ThrottleSendReceiptJob: order not found ' . $this->orderId);
            return;
        }

        $key = 'resend-receipt:' . $order->id . ':' . ($order->customer->id ?? 'guest');
        if (RateLimiter::tooManyAttempts($key, 3)) {
            Log::warning('ThrottleSendReceiptJob: too many resend attempts for order ' . $order->id);
            return;
        }

        RateLimiter::hit($key, 60*60);
        // Dispatch actual send job
        SendReceiptEmailJob::dispatch($order->id);
    }
}
