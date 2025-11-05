<?php

namespace App\Console;

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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('refresh:database')->twiceDaily(1, 13)->withoutOverlapping();

        // Run daily at 1:20 AM PKT (GMT+5)
        // $schedule->command('violation:process')->dailyAt('01:20')->withoutOverlapping();
        // $schedule->command('test:cron')->everyMinute();
        $schedule->command('violation:process')
         ->dailyAt('02:10')
         ->timezone('Asia/Karachi');

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
