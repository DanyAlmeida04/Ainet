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

            // Attempt to store the file
            Storage::put($path, $output);

            // Try to obtain a full path via Storage (works with local driver)
            $full = null;
            try {
                if (method_exists(Storage::disk(), 'path')) {
                    $full = Storage::path($path);
                }
            } catch (\Throwable $e) {
                $full = storage_path('app/' . $path);
            }

            // If file exists or Storage reports it, update DB with relative path regardless
            $saved = false;
            if ($full && file_exists($full)) {
                $saved = true;
            } else {
                // As a last check, use Storage::exists
                if (Storage::exists($path)) {
                    $saved = true;
                    // Try to get full path if not already
                    try { $full = Storage::path($path); } catch (\Throwable $e) { $full = storage_path('app/' . $path); }
                }
            }

            // Always set receipt_url to the relative storage path so preview/download can find it via Storage
            $order->receipt_url = $path;
            $order->save();

            if ($saved) {
                // Try to log size
                try {
                    $size = Storage::size($path);
                } catch (\Throwable $e) {
                    $size = strlen($output);
                }
                Log::info('GenerateReceiptJob: saved receipt for order ' . $order->id . ' -> ' . $path . ' (' . $size . ' bytes)');
            } else {
                Log::warning('GenerateReceiptJob: stored via Storage::put but file not found by file_exists for order ' . $order->id . ' ; receipt_url written as ' . $path);
            }

        } catch (\Throwable $e) {
            Log::error('GenerateReceiptJob error for order ' . ($order->id ?? $this->orderId) . ': ' . $e->getMessage());
        }
    }
}
