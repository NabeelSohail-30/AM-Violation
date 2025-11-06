<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ViolationApiController;

class VerifyAddressCommand extends Command
{
    protected $signature = 'violation:verify-address';
    protected $description = 'Verify unverified violation addresses using Smarty API';

    public function handle()
    {
        $controller = new ViolationApiController();

        $records = DB::table('violation_records')
            ->whereNull('click2mail_job_id')
            ->where('is_address_verify', 0)
            ->limit(100)
            ->get();

        if ($records->isEmpty()) {
            $this->info("No unverified records found.");
            return;
        }

        foreach ($records as $record) {
            try {
                $this->info("Verifying address for record ID: {$record->id}");
                Log::info("Verifying address for record ID: {$record->id}");

                $address = [
                    'address1' => $record->address1,
                    'address2' => $record->address2,
                    'state'    => $record->state,
                    'zip'      => $record->zip,
                ];

                $result = $controller->validate_US_address($address);

                DB::table('violation_records')
                    ->where('id', $record->id)
                    ->update(['is_address_verify' => $result]);

                if ($result == 1) {
                    $this->info("✅ Address verified for record ID: {$record->id}");
                } elseif ($result == 2) {
                    $this->warn("❌ No match found for record ID: {$record->id}");
                } else {
                    $this->error("⚠️ API error verifying record ID: {$record->id}");
                }
            } catch (\Exception $e) {
                Log::error("Verification failed for record {$record->id}: " . $e->getMessage());
                $this->warn("Verification failed for record {$record->id}");
            }
        }

        $this->info("Address verification process completed.");
    }
}
