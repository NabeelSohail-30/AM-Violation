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
        // $schedule->command('violation:process')
        //  ->dailyAt('02:10')
        //  ->timezone('Asia/Karachi');

        // 1️⃣ Fetch Violations - Every Friday midnight (full data)
        $schedule->command('violation:fetch')
            ->weeklyOn(5, '0:00')
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/fetch.log'));

        // 2️⃣ Verify Addresses - Saturday → Thursday, every 3 hours, 100 records per run
        $schedule->command('violation:verify-address')
            ->cron('0 */3 * * 6-4') // every 3 hours from Sat to Thu
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/verify.log'));

        // 3️⃣ Create Click2Mail Jobs - Saturday → Friday, every 4 hours, 100 records per run
        $schedule->command('violation:create-click2mail-job')
            ->cron('0 */4 * * 6-5')
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/create_jobs.log'));

        // 4️⃣ Submit Jobs - Every Tuesday + Friday midnight, 100 records per run
        $schedule->command('click2mail:submit-job')
            ->weeklyOn(2, '0:00') // Tuesday midnight
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/submit_jobs_tue.log'));

        $schedule->command('click2mail:submit-job')
            ->weeklyOn(5, '0:00') // Friday midnight
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/submit_jobs_fri.log'));

        // 5️⃣ Daily Job Status Update - Every day at 2 PM
        $schedule->command('click2mail:check-job-status')
            ->dailyAt('14:00')
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/status_update.log'));
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
