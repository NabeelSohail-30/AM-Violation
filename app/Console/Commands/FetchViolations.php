<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ViolationApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FetchViolations extends Command
{
    protected $signature = 'violation:fetch {api_id?}';
    protected $description = 'Fetch violation records from APIs and insert into DB';

    public function handle()
    {
        $controller = new ViolationApiController();
        $apis = DB::table('violation_api')->get();

        foreach ($apis as $api) {
            if ($this->argument('api_id') && $this->argument('api_id') != $api->id) continue;

            $this->info("Fetching for API ID {$api->id}");
            Log::info("Running fetch for API ID {$api->id}");

            $controller->fetch_records_cron($api->id);
        }

        $this->info('✅ All API fetches completed');
    }
}
