<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ViolationApiController;


class ProcessViolationJobs extends Command
{
    protected $signature = 'violation:process';
    protected $description = 'Fetch violations, validate addresses, and create Click2Mail jobs daily';

    public function handle()
    {
        $controller = new ViolationApiController();

        $apis = DB::table('violation_api')->where('is_active', 1)->get();

        foreach ($apis as $api) {
            try {
                $this->info("Processing API ID: {$api->id}");
                Log::info("Processing API ID: {$api->id}");

                // 1️⃣ Fetch records (cron-safe)
                $controller->fetch_records_cron($api->id);

                // 2️⃣ Get newly fetched and verified records that haven't been sent to Click2Mail
                $records = DB::table('violation_records')
                    ->where('violation_type', $api->violation_type)
                    ->where('is_address_verify', 1) // Valid addresses only
                    ->where('is_send_mail', 0) // Not sent yet
                    ->get();

                foreach ($records as $record) {
                    try {
                        // Direct call to create_job passing record id
                        $response = $controller->create_job_cron($record->id);

                        if ($response['success'] ?? false) {
                            DB::table('violation_records')->where('id', $record->id)
                                ->update(['is_send_mail' => 2]); // Sent
                            $this->info("Job created for record ID: {$record->id}, Job ID: ".$response['jobId']);
                            Log::info("Job created for record ID: {$record->id}, Job ID: ".$response['jobId']);
                        } else {
                            $this->warn("Failed to create job for record ID: {$record->id}");
                            Log::warning("Failed to create job for record ID: {$record->id}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Job creation error for record {$record->id}: ".$e->getMessage());
                        $this->warn("Job creation error for record {$record->id}");
                    }
                }

            } catch (\Exception $e) {
                Log::error("API processing error for API {$api->id}: ".$e->getMessage());
                $this->warn("API processing error for API {$api->id}");
            }
        }

        $this->info("Violation processing completed.");
        Log::info("Violation processing completed.");
    }
}
