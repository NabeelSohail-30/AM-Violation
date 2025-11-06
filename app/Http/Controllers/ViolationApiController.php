<?php

namespace App\Http\Controllers;

use App\DataTables\ViolationApiDataTable;
use App\DataTables\ViolationRecordsDataTable;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class ViolationApiController extends Controller
{
    private $boro_map = [
        '1' => 'Manhattan',
        '2' => 'Bronx',
        '3' => 'Brooklyn',
        '4' => 'Queens',
        '5' => 'Staten Island',
        'MN' => 'Manhattan',
        'BX' => 'Bronx',
        'BK' => 'Brooklyn',
        'QN' => 'Queens',
        'SI' => 'Staten Island',
    ];

    private $api_mappings = [
        1 => [
            'house_number' => 'house_number',
            'street'       => 'street',
            'boro'         => 'boro',
            'zip'          => 'zip',
            'date_field'   => 'issue_date',
        ],
        2 => [
            'house_number' => 'respondent_house_number',
            'street'       => 'respondent_street',
            'boro'         => 'boro',
            'zip'          => 'respondent_zip',
            'date_field'   => 'issue_date',
        ],
        3 => [
            'house_number' => 'housenumber',
            'street'       => 'streetname',
            'boro'         => 'boro',
            'zip'          => 'zip',
            'date_field'   => 'novissueddate',
        ],
        4 => [
            'house_number' => 'street',
            'street'       => 'street_name',
            'boro'         => 'boro',
            'zip'          => 'postcode',
            'date_field'   => 'vio_date',
        ],
        5 => [
            'house_number' => 'house_number',
            'street'       => 'street',
            'boro'         => 'borough',
            'zip'          => 'zip',
            'date_field'   => 'violation_issue_date',
        ],
    ];

    function normalize_address(array $data, $source)
    {
        $mapping = $this->api_mappings[$source] ?? null;

        if (!$mapping) {
            throw new Exception("Unknown API source: {$source}");
        }

        return [
            'house_number' => $data[$mapping['house_number']] ?? '',
            'street'       => $data[$mapping['street']] ?? '',
            'boro'         => $data[$mapping['boro']] ?? '',
            'zip'          => $data[$mapping['zip']] ?? '',
            'state'        => 'NY', // default for NYC
            'issue_date'   => $data[$mapping['date_field']] ?? null,
        ];
    }

    /**
     * Fetch data from external violation APIs, validate addresses, and save into DB.
     */
    public function fetch_records_cron($api_id)
    {
        ini_set('max_execution_time', 1800); // 30 mins

        $record = DB::table('violation_api')->find($api_id);

        if (!$record) {
            Log::error("Violation API not found: ID {$api_id}");
            return false;
        }

        $url = $record->url;
        $field = $record->fetch_param;

        $lastFetched = $record->last_fetched_at;
        $day_start = $lastFetched ? Carbon::parse($lastFetched)->format('Y-m-d\TH:i:s') : Carbon::now()->subDays(7)->format('Y-m-d\TH:i:s');
        $day_end = Carbon::now()->format('Y-m-d\TH:i:s');
        $condition = "$field between '$day_start' and '$day_end'";
        $api_url = $url . '?' . $condition;

        try {
            $response = Http::get($api_url);

            if (!$response->successful()) {
                Log::error("API request failed: {$api_url}");
                return false;
            }

            $data = $response->json();
            $user_id = 1; // System user for cron

            if (empty($data)) {
                DB::table('violation_api')->where('id', $api_id)->update([
                    'last_fetched_at' => now(),
                    'last_fetch_count' => 0,
                    'updated_date' => now(),
                    'updated_by' => $user_id,
                ]);
                Log::info("No records found for API ID {$api_id}");
                return true;
            }

            DB::beginTransaction();

            foreach ($data as $item) {
                try {
                    $item['state'] = 'NY';
                    $address = $this->normalize_address($item, $record->violation_type);
                    $validate_address = $this->validate_US_address($address);

                    // Parse dynamic issue date safely
                    $issue_date = null;
                    if (!empty($address['issue_date'])) {
                        try {
                            $issue_date = Carbon::parse($address['issue_date'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $issue_date = null;
                        }
                    }

                    DB::table('violation_records')->insert([
                        'violation_type'    => $record->violation_type ?? 0,
                        'violation_api'     => $record->id ?? 0,
                        'issue_date'        => $issue_date,
                        'address1'          => trim(($address['house_number'] ?? '') . ' ' . ($address['street'] ?? '')),
                        'address2'          => $this->boro_map[$address['boro']] ?? $address['boro'] ?? '',
                        'state'             => $address['state'] ?? 'NY',
                        'zip'               => $address['zip'] ?? '',
                        'json'              => json_encode($item) ?? '{}',
                        'is_address_verify' => $validate_address ?? 0,
                        'created_by'        => $user_id,
                        'created_date'      => now(),
                    ]);

                    Log::info("Inserted violation record for API ID {$api_id}");
                } catch (\Exception $e) {
                    Log::error("Insert failed for API ID {$api_id}: " . $e->getMessage());
                }
            }

            // Update API fetch info
            DB::table('violation_api')->where('id', $api_id)->update([
                'last_fetched_at' => now(),
                'last_fetch_count' => count($data),
                'updated_date' => now(),
                'updated_by' => $user_id,
            ]);

            DB::commit();
            Log::info("Fetch & insert completed for API ID {$api_id}");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fetch error for API ID {$api_id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate an address using SmartyStreets API
     */
    function validate_US_address(array $address)
    {
        $params = [
            "street"   => trim($address['address1'] ?? ''),
            "city"     => trim($address['address2'] ?? ''),  // <-- use address2 as city
            "state"    => $address['state'] ?? 'NY',
            "zipcode"  => $address['zip'] ?? '',
            "auth-id"  => env('SMARTY_AUTH_ID'),
            "auth-token" => env('SMARTY_AUTH_TOKEN'),
        ];


        $endpoint = "https://us-street.api.smarty.com/street-address";

        Log::info("Smarty API Request Parameters:", $params);

        try {
            $response = Http::timeout(30)->get($endpoint, $params);

            Log::info("Smarty Raw Response:", ['body' => $response->body()]);

            if ($response->successful()) {
                $json = $response->json();
                Log::info("Smarty Parsed JSON:", $json);
                return !empty($json) ? 1 : 2;
            }

            Log::warning("Smarty API Error Response:", $response->json());
            return 0;
        } catch (\Exception $e) {
            Log::error("SmartyStreets API Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create a job on Click2Mail and save job details in DB
     */
    public function create_job(Request $request)
    {
        // Fetch the violation record
        $record = DB::table('violation_records')->find($request->record_id);
        if (!$record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Setup Click2Mail authentication
        $auth = 'Basic ' . base64_encode(env('CLICK2MAIL_USERNAME') . ':' . env('CLICK2MAIL_PASSWORD'));
        $base_url = rtrim(env('CLICK2MAIL_BASE_URL'), '/');
        $documentId = env('DOCUMENT_ID');

        try {
            // =============================
            // 1️⃣ CREATE ADDRESS LIST
            // =============================
            $xmlBody = "
            <addressList>
                <addressListName>Auto Address</addressListName>
                <addressMappingId>1</addressMappingId>
                <addresses>
                    <address>
                        <Firstname>No Name</Firstname>
                        <Lastname></Lastname>
                        <Address1>" . htmlspecialchars($record->address1 ?? '') . "</Address1>
                        <Address2>" . htmlspecialchars($record->address2 ?? '') . "</Address2>
                        <City>" . htmlspecialchars($record->city ?? 'Unknown') . "</City>
                        <State>" . htmlspecialchars($record->state ?? '') . "</State>
                        <Postalcode>" . htmlspecialchars($record->zip ?? '') . "</Postalcode>
                        <Country>US</Country>
                    </address>
                </addresses>
            </addressList>";

            $addressResponse = Http::withHeaders([
                'Authorization' => $auth,
                'Content-Type'  => 'application/xml'
            ])->withBody($xmlBody, 'application/xml')
                ->post("{$base_url}/addressLists");

            $addressBody = $addressResponse->body();

            if (!$addressResponse->successful()) {
                Log::error("❌ Address List Creation Failed", [
                    'record_id' => $record->id,
                    'response' => $addressBody
                ]);
                return response()->json(['error' => 'Address list creation failed'], 500);
            }

            // Parse response (XML or JSON)
            if (str_starts_with(trim($addressBody), '<')) {
                $xml = simplexml_load_string($addressBody);
                $addressListId = (string) ($xml->id ?? null);
            } else {
                $json = json_decode($addressBody, true);
                $addressListId = $json['id'] ?? null;
            }

            if (!$addressListId) {
                Log::error("❌ No addressListId found in response", ['response' => $addressBody]);
                return response()->json(['error' => 'No address list ID found'], 500);
            }

            Log::info("✅ Address List Created Successfully", [
                'record_id' => $record->id,
                'addressListId' => $addressListId
            ]);

            // 2️⃣ CREATE JOB - Enhanced payload
            $jobPayload = [
                "documentClass"  => "Letter 8.5 x 11",
                "layout"         => "Address on Separate Page",
                "productionTime" => "Next Day",
                "color"          => "Black and White",
                "paperType"      => "White 24#",
                "printOption"    => "Printing One Side",
                "documentId"     => $documentId,
                "addressId"      => $addressListId,
                "mailClass"      => "First Class",
                "billingMethod"  => "PREPAID", // Add this
                "envelope"       => "No. 10 Regular", // Add this
            ];

            Log::info("📦 Creating Click2Mail Job", [
                'record_id' => $record->id,
                'payload' => $jobPayload
            ]);

            $jobResponse = Http::withHeaders([
                'Authorization' => $auth,
                'Content-Type'  => 'application/json'
            ])->timeout(30)->post("{$base_url}/jobs", $jobPayload);

            // Enhanced error handling
            if (!$jobResponse->successful()) {
                $errorBody = $jobResponse->body();
                Log::error("❌ Click2Mail Job Creation Failed", [
                    'record_id' => $record->id,
                    'status' => $jobResponse->status(),
                    'response' => $errorBody,
                    'payload' => $jobPayload
                ]);

                return response()->json([
                    'error' => 'Failed to create job',
                    'details' => json_decode($errorBody, true) ?: $errorBody
                ], 500);
            }

            $jobData = $jobResponse->json();
            $jobId = $jobData['id'] ?? null;

            if (!$jobId) {
                Log::error("❌ No jobId returned", ['response' => $jobData]);
                return response()->json(['error' => 'No job ID returned'], 500);
            }

            $jobStatus = $jobData['status'];

            // =============================
            // 3️⃣ UPDATE DATABASE
            // =============================
            DB::table('violation_records')->where('id', $record->id)->update([
                'click2mail_job_id'     => $jobId,
                'click2mail_job_status' => $jobStatus,
                'click2mail_created_at' => now()
            ]);

            Log::info("✅ Click2Mail Job Created Successfully", [
                'record_id' => $record->id,
                'job_id' => $jobId,
                'status' => $jobStatus
            ]);

            return response()->json([
                'success' => true,
                'jobId'   => $jobId,
                'status'  => $jobStatus
            ]);
        } catch (\Exception $e) {
            Log::error("💥 Click2Mail Job Creation Exception for Record {$record->id}: " . $e->getMessage());
            return response()->json([
                'error' => 'Job creation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function create_job_cron($record_id)
    {
        $request = new \Illuminate\Http\Request();
        $request->merge(['record_id' => $record_id]);

        $response = $this->create_job($request);

        // Convert JsonResponse to array
        return $response instanceof \Illuminate\Http\JsonResponse
            ? $response->getData(true)
            : ['success' => false];
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViolationApiDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Violation APi')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '';
        return $dataTable->render('violation_api.list', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Display a listing of the violation records.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_violation_records(ViolationRecordsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Violation Records')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '';
        return $dataTable->render('violation_records.list', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }
}
