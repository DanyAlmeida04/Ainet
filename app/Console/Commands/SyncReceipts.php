<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;

class SyncReceipts extends Command
{
    protected $signature = 'orders:sync-receipts {--dir=private/pdf_receipts : Directory under storage/app to scan}';
    protected $description = 'Scan storage for existing PDF receipts and update orders.receipt_url when missing or incorrect.';

    public function handle()
    {
        $dir = trim($this->option('dir'), '/');
        $this->info('Scanning storage/app/' . $dir);

        if (! Storage::exists($dir)) {
            $this->warn('Directory does not exist: ' . $dir);
            return 1;
        }

        $files = Storage::files($dir);
        $this->info('Found ' . count($files) . ' files.');

        $updated = 0;
        foreach ($files as $f) {
            $base = basename($f);
            // match receipt_{id}.pdf
            if (preg_match('/receipt_(\d+)\.pdf$/', $base, $m)) {
                $id = intval($m[1]);
                $order = Order::find($id);
                if (! $order) {
                    $this->line("File $f -> no order #$id found");
                    continue;
                }

                $rel = $dir . '/' . $base;
                if ($order->receipt_url !== $rel) {
                    $order->receipt_url = $rel;
                    $order->save();
                    $this->line("Updated order #$id -> $rel");
                    $updated++;
                } else {
                    $this->line("Order #$id already pointing to $rel");
                }
            }
        }

        $this->info('Done. Updated: ' . $updated);
        return 0;
    }
}
