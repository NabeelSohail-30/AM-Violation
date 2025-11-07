<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckClick2MailJobStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'click2mail:check-job-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of Click2Mail jobs from the database and log the results.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch all records with job IDs
        $records = DB::table('violation_records')
            ->whereNotNull('click2mail_job_id')
            ->get();

        foreach ($records as $record) {
            $this->checkJobStatus($record->click2mail_job_id, $record->id);
        }

        $this->info('Job status checks complete.');
    }

    /**
     * Query Click2Mail for the job status.
     *
     * @param string $jobId
     * @param int $recordId
     * @return void
     */
    protected function checkJobStatus($jobId, $recordId)
    {
        $username = env('CLICK2MAIL_USERNAME');
        $password = env('CLICK2MAIL_PASSWORD');
        $base_url = rtrim(env('CLICK2MAIL_BASE_URL'), '/');

        try {
            $response = Http::withBasicAuth($username, $password)
                ->get("{$base_url}/jobs/{$jobId}");

            // Log the raw XML response for debugging
            Log::info("Raw Click2Mail Job Status Response for Record ID {$recordId}: " . $response->body());

            if ($response->successful()) {
                // Parse the XML response
                $xml = simplexml_load_string($response->body());

                // Check if parsing was successful
                if ($xml && isset($xml->status)) {
                    $jobStatus = (string) $xml->status; // 'Editing', 'Created', etc.
                    $jobDescription = (string) $xml->description; // e.g., 'Job is being processed'
                    $statusUrl = (string) $xml->statusUrl; // URL to track status

                    // Log parsed job status
                    Log::info("Job status for Record ID | JobID: {$recordId}|{$jobId} -- Status:{$jobStatus} -- Desc: {$jobDescription}");

                    // Optional: Update the status in the database if you need to store it
                    DB::table('violation_records')->where('id', $recordId)->update(['click2mail_job_status' => $jobDescription]);

                } else {
                    Log::error("Failed to parse XML response for Job ID {$jobId}. Response: {$response->body()}");
                }
            } else {
                Log::error("Failed to fetch status for Job ID {$jobId}. Response: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Error checking status for Job ID {$jobId}: {$e->getMessage()}");
        }
    }
}
