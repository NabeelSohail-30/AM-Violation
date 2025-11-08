<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubmitClick2MailJob extends Command
{
    protected $signature = 'click2mail:submit-job';
    protected $description = 'Submit one Click2Mail job (status Editing) for testing.';

    public function handle()
    {
        Log::info('🚀 Starting Click2Mail job submission for up to 100 jobs...');

        $username = env('CLICK2MAIL_USERNAME');
        $password = env('CLICK2MAIL_PASSWORD');
        $base_url = rtrim(env('CLICK2MAIL_BASE_URL'), '/');

        // Fetch up to 100 jobs where status = Editing
        $jobs = DB::table('violation_records')
            ->where('click2mail_job_status', 'Editing')
            ->whereNotNull('click2mail_job_id')
            ->limit(30)
            ->get();

        if ($jobs->isEmpty()) {
            Log::warning('⚠️ No jobs found with status = Editing.');
            $this->info('No jobs found with status = Editing.');
            return;
        }

        foreach ($jobs as $job) {
            $jobId = $job->click2mail_job_id;
            $recordId = $job->id;

            try {
                $response = Http::withBasicAuth($username, $password)
                    ->post("{$base_url}/jobs/{$jobId}/submit?billingType=User%20Credit");

                Log::info('📤 Job Submission Attempt', [
                    'record_id' => $recordId,
                    'job_id' => $jobId,
                    'status_code' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body(),
                ]);

                if ($response->successful()) {
                    Log::info("✅ Job successfully submitted for Record ID {$recordId}.");
                    $this->info("Job submitted successfully for Record ID {$recordId}.");

                    // (Optional) Update DB to mark as submitted
                    DB::table('violation_records')
                        ->where('id', $recordId)
                        ->update([
                            'click2mail_job_status' => 'Awaiting Production',
                            'updated_date' => now()
                        ]);
                } else {
                    Log::error("❌ Failed to submit Job ID {$jobId}.", [
                        'record_id' => $recordId,
                        'response' => $response->body()
                    ]);
                    $this->error("Failed to submit Job ID {$jobId}.");
                }
            } catch (\Exception $e) {
                Log::error("💥 Exception while submitting Job ID {$jobId}: {$e->getMessage()}");
                $this->error("Exception for Job ID {$jobId}: " . $e->getMessage());
            }
        }

        Log::info('✅ Click2Mail job submission batch process completed.');
        $this->info('All eligible jobs have been processed.');
    }
}
