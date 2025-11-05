<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCron extends Command
{
    protected $signature = 'test:cron';
    protected $description = 'Dummy command to test cron';

    public function handle()
    {
        Log::info("TestCron executed at " . now());
        $this->info("TestCron executed at " . now());
    }
}
