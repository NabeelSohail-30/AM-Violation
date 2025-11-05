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
    private function validate_US_address(array $address)
    {
        $endpoint = "https://us-street.api.smarty.com/street-address";

        try {
            $response = Http::get($endpoint, [
                "street" => trim(($address['house_number'] ?? '') . " " . ($address['street'] ?? '')),
                "city" => $this->boro_map[$address['boro']] ?? $address['boro'] ?? '',
                "state" => $address['state'] ?? 'NY',
                "zipcode" => $address['zip'] ?? '',
                "key" => env('SMARTY_API_KEY')
            ]);

            if ($response->successful()) {
                return !empty($response->json()) ? 1 : 2;
            }

            return 0;
        } catch (Exception $e) {
            Log::error("Smarty API error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create a job on Click2Mail and save job details in DB
     */
    public function create_job(Request $request)
    {
        $record = DB::table('violation_records')->find($request->record_id);
        if (!$record) return response()->json(['error' => 'Record not found'], 404);

        $auth = 'Basic ' . base64_encode(env('CLICK2MAIL_USERNAME') . ':' . env('CLICK2MAIL_PASSWORD'));
        $base_url = env('CLICK2MAIL_BASE_URL');
        $documentId = env('DOCUMENT_ID');

        try {
            // Create Address List
            $xmlBody = "<addressList>
                <addressListName>Auto Address</addressListName>
                <addressMappingId>1</addressMappingId>
                <addresses>
                    <address>
                        <Firstname>No Name</Firstname>
                        <Lastname></Lastname>
                        <Address1>{$record->address1}</Address1>
                        <Address2>{$record->address2}</Address2>
                        <City>{$record->address2}</City>
                        <State>{$record->state}</State>
                        <Postalcode>{$record->zip}</Postalcode>
                    </address>
                </addresses>
            </addressList>";

            $addressResponse = Http::withHeaders([
                'Authorization' => $auth,
                'Content-Type' => 'application/xml'
            ])->withBody($xmlBody, 'application/xml')
                ->post("{$base_url}/addressLists");

            $xml = simplexml_load_string($addressResponse->body());
            $addressId = (string) ($xml->id ?? '');

            if (!$addressId) {
                return response()->json(['error' => 'Failed to upload address list'], 500);
            }

            // Create Job
            $jobResponse = Http::withHeaders([
                'Authorization' => $auth,
                'Content-Type' => 'application/json'
            ])->post("{$base_url}/jobs", [
                "documentClass" => "Letter 8.5 x 11",
                "layout" => "Address on Separate Page",
                "productionTime" => "Next Day",
                "color" => "Black and White",
                "paperType" => "White 24#",
                "printOption" => "Printing One Side",
                "documentId" => $documentId,
                "addressId" => $addressId,
                "mailClass" => "First Class"
            ]);

            $jobData = $jobResponse->json();
            $jobId = $jobData['id'] ?? null;
            $jobStatus = $jobData['status'] ?? 'Editing';

            DB::table('violation_records')->where('id', $record->id)->update([
                'click2mail_job_id' => $jobId,
                'click2mail_job_status' => $jobStatus,
            ]);

            return response()->json(['success' => true, 'jobId' => $jobId, 'status' => $jobStatus]);
        } catch (Exception $e) {
            Log::error("Click2Mail Error: " . $e->getMessage());
            return response()->json(['error' => 'Job creation failed', 'message' => $e->getMessage()], 500);
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
