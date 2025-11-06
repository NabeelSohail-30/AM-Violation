<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ViolationApiController;

class CreateClick2MailJob extends Command
{
    protected $signature = 'violation:create-click2mail-job';
    protected $description = 'Create Click2Mail jobs for verified violation records without job ID';

    public function handle()
    {
        $controller = new ViolationApiController();

        // Fetch unprocessed verified records
        $records = DB::table('violation_records')
            ->where('is_address_verify', 1)
            ->whereNull('click2mail_job_id')
            ->limit(100)
            ->get();

        if ($records->isEmpty()) {
            $this->info("No records pending for Click2Mail job creation.");
            return;
        }

        foreach ($records as $record) {
            $this->info("📨 Creating Click2Mail job for record ID: {$record->id}");
            Log::info("📨 Creating Click2Mail job for record ID: {$record->id}");

            try {
                $result = $controller->create_job_cron($record->id);

                if (!empty($result['success']) && $result['success'] === true) {
                    $this->info("✅ Job Created Successfully (Job ID: {$result['jobId']})");
                    Log::info("✅ Click2Mail Job Created for Record {$record->id}", $result);
                } else {
                    $this->warn("⚠️ Failed to create job for Record ID: {$record->id}");
                    Log::warning("⚠️ Job creation failed", ['record_id' => $record->id, 'response' => $result]);
                }
            } catch (\Exception $e) {
                $this->error("❌ Error creating job for Record ID: {$record->id} - " . $e->getMessage());
                Log::error("❌ Exception creating Click2Mail job for Record {$record->id}: " . $e->getMessage());
            }
        }

        $this->info("🎯 Click2Mail job creation process completed.");
    }
}
