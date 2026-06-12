<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RegenerateReceipts extends Command
{
    protected $signature = 'orders:regenerate-receipts {--days=0 : Limit orders to last N days} {--force : Regenerate even if receipt exists}';
    protected $description = 'Regenerate PDF receipts for orders missing a receipt or when --force is used';

    public function handle()
    {
        $query = Order::query();
        if ($this->option('days') && intval($this->option('days')) > 0) {
            $d = intval($this->option('days'));
            $query->where('date', '>=', now()->subDays($d));
        }

        $orders = $query->orderBy('date', 'desc')->get();
        $this->info('Found ' . $orders->count() . ' orders to inspect.');

        foreach ($orders as $order) {
            try {
                $this->line('Order #' . $order->id . ' - receipt_url: ' . ($order->receipt_url ?? 'NULL'));

                $shouldGenerate = $this->option('force') || empty($order->receipt_url);
                // also check if file exists
                if (!empty($order->receipt_url)) {
                    $possible = ltrim($order->receipt_url, '/');
                    if (!Storage::exists($possible)) {
                        $shouldGenerate = true;
                    }
                }

                if (! $shouldGenerate) {
                    $this->info('  Skipping (already has file).');
                    continue;
                }

                // load items relation to ensure view has data
                $order->load('items');
                $pdf = Pdf::loadView('orders.receipt', compact('order'));
                try { $pdf->setPaper('A4'); } catch (\Throwable $e) {}
                $output = $pdf->output();

                $dir = 'private/pdf_receipts';
                Storage::makeDirectory($dir);
                $path = $dir . '/receipt_' . $order->id . '.pdf';
                Storage::put($path, $output);

                // Always update DB with relative path so preview/download can use Storage
                $order->receipt_url = $path;
                $order->save();

                // Verify existence via Storage
                if (Storage::exists($path)) {
                    $this->info('  Regenerated and saved to ' . $path);
                } else {
                    $this->warn('  Stored but could not verify file via Storage::exists for order ' . $order->id . ' (path: ' . $path . ')');
                }
            } catch (\Throwable $e) {
                Log::error('Error regenerating receipt for order ' . $order->id . ': ' . $e->getMessage());
                $this->error('  Error: ' . $e->getMessage());
            }
        }

        $this->info('Done.');
        return 0;
    }
}
