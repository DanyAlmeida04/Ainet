<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Jobs\GenerateReceiptJob;

class GenerateSingleReceipt extends Command
{
    protected $signature = 'orders:generate-receipt {order_id}';
    protected $description = 'Generate receipt PDF for a single order ID (uses existing job).';

    public function handle()
    {
        $id = $this->argument('order_id');
        $order = Order::find($id);
        if (! $order) {
            $this->error('Order not found: ' . $id);
            return 1;
        }

        try {
            GenerateReceiptJob::dispatchSync($order->id);
            $this->info('Receipt generation dispatched for order ' . $order->id);
            return 0;
        } catch (\Throwable $e) {
            $this->error('Error generating receipt: ' . $e->getMessage());
            return 1;
        }
    }
}
