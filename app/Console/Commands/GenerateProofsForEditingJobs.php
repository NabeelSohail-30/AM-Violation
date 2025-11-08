<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GenerateProofsForEditingJobs extends Command
{
    protected $signature = 'click2mail:generate-proof';

    protected $description = 'Generate proof for jobs with Editing status from Click2Mail';

    public function handle()
    {
        // 1️⃣ Fetch the first job with status 'Editing' from the database (for testing purposes, limit to one job)
        $job = DB::table('violation_records')
            ->where('click2mail_job_status', 'Editing')
            ->first();

        if (!$job) {
            Log::info('No jobs found with Editing status.');
            return;
        }

        // 2️⃣ Prepare Click2Mail request to generate proof
        $jobId = $job->click2mail_job_id;

        Log::info("Requesting proof for Job ID {$jobId}...");

        $username = env('CLICK2MAIL_USERNAME');
        $password = env('CLICK2MAIL_PASSWORD');
        $base_url = rtrim(env('CLICK2MAIL_BASE_URL'), '/');

        try {
            // Create Proof Request
            $response = Http::withBasicAuth($username, $password)
                ->post("{$base_url}/jobs/{$jobId}/proof");

            Log::info("📦 Proof Creation Attempt", [
                'job_id' => $jobId,
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);


            if ($response->successful()) {
                Log::info("Proof successfully created for Job ID {$jobId}.");
            } else {
                Log::error("Failed to create proof for Job ID {$jobId}. Response: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Error requesting proof for Job ID {$jobId}: {$e->getMessage()}");
        }
    }
}
