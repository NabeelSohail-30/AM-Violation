<?php

namespace App\Http\Controllers;

use App\DataTables\ViolationApiDataTable;
use App\DataTables\ViolationRecordsDataTable;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use App\Models\ViolationApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ViolationApiController extends Controller
{
    private  $boro_map = [
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
     * Fetch a specific violation API record and update it via external API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch_records(Request $request)
    {
        ini_set('max_execution_time', 1800); // 5 minutes
        $id = $request->id;

        $record = ViolationApi::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }

        $url   = $record->url;
        $field = $record->fetch_param;

        // Date range (last 1 day)
        $day_start = Carbon::now()->subDays(60)->startOfDay()->format('Y-m-d\TH:i:s');
        $day_end   = Carbon::now()->subDays(2)->startOfDay()->format('Y-m-d\TH:i:s');

        // Where condition (dynamic column name)
        $condition = "$field between '$day_start' and '$day_end'";

        // Final API URL
        $api_url = $url . '?' . $condition;

        // Request
        $response = Http::get($api_url);

        if (!$response->successful()) {

            Log::error("API request failed: URL {$api_url} Status {$response->status()}");
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        } else {

            $user_id = auth()->id();

            try {

                $data = $response->json();

                if (!empty($data)) {

                    DB::beginTransaction();
                    foreach ($data as $item) {

                        $item['state'] = 'NY';
                        $address = $this->normalize_address($item, $record->violation_type);

                        $validate_address = $this->validate_US_address($address);

                        // Parse dynamic issue date
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
                            'address2'          => $this->boro_map[$address['boro']] ?? $address['boro'] ?? null,
                            'state'             => $address['state'] ?? 'NY',
                            'zip'               => $address['zip'] ?? '',
                            'json'              => json_encode($item) ?? null,
                            'is_address_verify' => $validate_address ?? 0,
                            'created_by'        => $user_id,
                        ]);
                    }

                    DB::table('violation_api')
                        ->where('id', $record->id)
                        ->update([
                            'last_fetched_at' => now(),
                            'last_fetch_count' => count($data),
                            'updated_date'      => now(),
                            'updated_by'      => $user_id,
                        ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Data fetched and saved successfully',
                        'records_inserted' => count($data),
                    ]);
                } else {

                    DB::table('violation_api')
                        ->where('id', $record->id)
                        ->update([
                            'last_fetched_at' => now(),
                            'last_fetch_count' => 0,
                            'updated_date'      => now(),
                            'updated_by'      => $user_id,
                        ]);

                    Log::warning('No Record Found');
                    return response()->json(['error' => 'No record found'], 404);
                }
            } catch (\Exception $e) {

                Log::error('Error fetching or saving API data: ' . $e->getMessage());
                DB::rollBack();
                return response()->json(['error' => 'Failed to save data'], 500);
            }
        }
    }

    /**
     * Validate a US address using SmartyStreets API.
     *
     * @param array $data ['house_number' => '', 'street' => '', 'boro' => '']
     * @return array|null
     */
    function validate_US_address(array $address)
    {

        $params = [
            "street" => trim(($address['house_number'] ?? '') . " " . ($address['street'] ?? '')),
            "city"   => $this->boro_map[$address['boro']] ?? $address['boro'] ?? '',
            "state"  => $address['state'] ?? 'NY',
            "zipcode" => $address['zip'] ?? '',
            "key"    => env('SMARTY_API_KEY')
        ];

        $endpoint = "https://us-street.api.smarty.com/street-address";

        try {
            $response = Http::withHeaders([
                'Referer' =>  url('/')
            ])->timeout(1800)->get($endpoint, $params);

            // dd($response->json());
            if ($response->successful()) {
                return !empty($response->json()) ? 1 : 2;
            }

            return 0;
        } catch (\Exception $e) {
            Log::error("SmartyStreets API Error: " . $e->getMessage());
            return 0;
        }
    }

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

    /**
     * Validate a US address using SmartyStreets API.
     *
     * @param request $request ['house_number' => '', 'street' => '', 'boro' => '']
     * @return array|null
     */
    function validate_specific_address(Request $request)
    {

        $user_id = auth()->id();
        if (!$request->id) {
            return response()->json(['error' => 'Invalid Request'], 500);
        }

        $record = DB::table('violation_records')->where('id', $request->id)->first(['address1', 'address2', 'state']);

        $params = [
            "street" => $record->address1 ?? '',
            "city"   => $record->address2 ?? '',
            "state"  => $record->state ?? '',
            "key"    => env('SMARTY_API_KEY')
        ];

        $endpoint = "https://us-street.api.smarty.com/street-address";

        try {
            $response = Http::withHeaders([
                'Referer' =>  url('/')
            ])->get($endpoint, $params);

            if ($response->successful()) {

                $data = ['updated_by' => $user_id, 'updated_date' => now()];
                if (!empty($response->json())) {

                    $data['is_address_verify'] = 1;
                    $status = 200;
                    $message = 'Address successsfully validate';
                    $success = true;
                } else {

                    $data['is_address_verify'] = 2;
                    $status = 422;
                    $message = 'Address is not valid';
                    $success = false;
                }

                DB::table('violation_records')
                    ->where('id', $request->id)
                    ->update($data);
                return response()->json(['success' => $success, 'message' => $message], $status);
            }
        } catch (\Exception $e) {
            Log::error("SmartyStreets API Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to validate address'], 500);
        }
    }


    public function send_mail(Request $request)
    {
        $auth_header = 'Basic ' . base64_encode(env('CLICK2MAIL_USERNAME') . ':' . env('CLICK2MAIL_PASSWORD'));
        $base_url = env('CLICK2MAIL_BASE_URL');
        $documentId = env('DOCUMENT_ID');

        // Fetch single violation record
        $record = DB::table('violation_records')->find($request->record_id);
        if (!$record) {
            return response()->json(['error' => 'Record not Found'], 404);
        }

        // Prepare CSV
        $csv = "name,address1,address2,city,state,zip\n";
        $csv .= "No Name,{$record->address1},{$record->address2},Unknown,{$record->state},{$record->zip}\n";

        try {

            $xmlBody = '<addressList>'
                .   '<addressListName>My Single-Address List</addressListName>'
                .   '<addressMappingId>1</addressMappingId>'
                .   '<addresses>'
                .     '<address>'
                .       '<Firstname>No Name</Firstname>'
                .       '<Lastname></Lastname>'
                .       '<Organization></Organization>'
                .       '<Address1>' . htmlspecialchars($record->address1) . '</Address1>'
                .       '<Address2>' . htmlspecialchars($record->address2) . '</Address2>'
                .       '<Address3></Address3>'
                .       '<City>' . htmlspecialchars($record->city ?? 'Unknown') . '</City>'
                .       '<State>' . htmlspecialchars($record->state) . '</State>'
                .       '<Postalcode>' . htmlspecialchars($record->zip) . '</Postalcode>'
                .       '<Country></Country>'
                .     '</address>'
                .   '</addresses>'
                . '</addressList>';

            $response = Http::withHeaders([
                'Authorization' => $auth_header,
                'Accept'        => 'application/xml',
                'Content-Type'  => 'application/xml',
            ])->withBody($xmlBody, 'application/xml')
                ->post("{$base_url}/addressLists");


            $body = $response->body();

            // 2️⃣ Parse response (XML or JSON)
            if (str_starts_with(trim($body), '<')) {
                $xml = simplexml_load_string($body);
                $addressId = (string) $xml->id ?? null;
            } else {
                $json = json_decode($body, true);
                $addressId = $json['addressId'] ?? null;
            }

            if (!$addressId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Address upload failed',
                    'response' => $body,
                    'status' => $response->status()
                ], 500);
            }


            // 3️⃣ Create Mailing Job
            $mailingResponse = Http::withHeaders([
                'Authorization' => $auth_header,
                'Content-Type' => 'application/json'
            ])->post("{$base_url}/jobs", [
                "documentClass" => "Letter 8.5 x 11",
                "layout" => "Address on Separate Page",
                "productionTime" => "Next Day",
                "color" => "Black and White",
                "paperType" => "White 24#",
                "printOption" => "Printing One Side",
                'documentId' => $documentId,
                'addressId' => $addressId,
                'mailClass' => 'First Class',
            ]);

            return $mailingResponse->json();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function upload_document()
    {

        $base_url = env('CLICK2MAIL_BASE_URL');
        $auth_header = 'Basic ' . base64_encode(env('CLICK2MAIL_USERNAME') . ':' . env('CLICK2MAIL_PASSWORD'));

        // 1️⃣ Local file path
        // $filePath = storage_path('app/public/banners/banner1.pdf'); // change file path as needed
        $filePath = public_path('banners/banner1.pdf');
        $fileName = 'violation_banner.pdf';

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Document not found on server'], 404);
        }

        // 2️⃣ Upload document to Click2Mail
        $response = Http::withHeaders([
            'Authorization' => $auth_header,
        ])->attach(
            'file',
            file_get_contents($filePath),
            $fileName
        )->asMultipart() // important
            ->post("{$base_url}/documents", [
                'documentName' => $fileName,
                'documentFormat' => 'PDF',
                'documentClass' => 'Letter 8.5 x 11',
            ]);

        $data = $response->json();

        // 3️⃣ Check response
        if (isset($data['documentId'])) {
            return response()->json([
                'success' => true,
                'documentId' => $data['documentId'],
                'response' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Document upload failed',
                'response' => $data
            ], 500);
        }
    }
}
