<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tries = 3;
    public array|int|null $backoff = [60, 300];

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::with('items')->find($this->orderId);
        if (! $order) {
            Log::warning('GenerateReceiptJob: order not found: ' . $this->orderId);
            return;
        }

        try {
            $pdf = Pdf::loadView('orders.receipt', compact('order'));
            try { $pdf->setPaper('A4'); } catch (\Throwable $e) {}
            $output = $pdf->output();

            $dir = 'private/pdf_receipts';
            Storage::makeDirectory($dir);
            $path = $dir . '/receipt_' . $order->id . '.pdf';
            Storage::put($path, $output);

            $full = storage_path('app/' . $path);
            if (file_exists($full)) {
                $order->receipt_url = $path;
                $order->save();
                Log::info('GenerateReceiptJob: saved receipt for order ' . $order->id . ' -> ' . $path);
            } else {
                Log::warning('GenerateReceiptJob: could not save receipt for order ' . $order->id);
            }
        } catch (\Throwable $e) {
            Log::error('GenerateReceiptJob error for order ' . $order->id . ': ' . $e->getMessage());
        }
    }
}
