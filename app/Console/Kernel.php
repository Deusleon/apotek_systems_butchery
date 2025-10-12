<?php

namespace App\Console;

use App\Console\Commands\CalculateStockValues;
use App\Console\Commands\CheckLowStock;
use App\Console\Commands\FixStockPermissions;
use App\Console\Commands\SnapshotOldStockValue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CalculateStockValues::class,
        CheckLowStock::class,
        FixStockPermissions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('stock:calculate-values')->hourly();
        $schedule->command('stock:check-low')->hourly();
        $schedule->command('snapshot:oldstock')->dailyAt('00:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
