<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\RegenerateReceipts::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('orders:regenerate-receipts')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Load commands from the Commands directory
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
