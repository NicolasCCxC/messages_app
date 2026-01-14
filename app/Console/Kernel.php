<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            Storage::deleteDirectory('tmp');
        })->daily();

        $schedule->command('validate:password-change-notification')->everyMinute();
        $schedule->command('search:inventoryNotifications')->daily();
        $schedule->command('validate:pay-transaction')->everyMinute();
        $schedule->command('deactivate:membership-cascade')->everyFiveMinutes();
        $schedule->command('validate:send-email-for-membership-finished')->daily();
        $schedule->command('pay:recurrent-payment-membership')->dailyAt('22:00');
        $schedule->command('update:invoices_available_company')->dailyAt('23:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
