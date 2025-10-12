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

class ViolationApiController extends Controller
{
    private $boro_map = [
        "1" => "Manhattan",   // Manhattan
        "2" => "Bronx",
        "3" => "Brooklyn",
        "4" => "Queens",
        "5" => "Staten Island"
    ];
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViolationApiDataTable $dataTable)
    {       
        $pageTitle = trans('global-message.list_form_title',['form' => trans('Violation APi')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '';
        return $dataTable->render('violation_api.list', compact('pageTitle','auth_user','assets', 'headerAction'));
    }

    /**
     * Fetch a specific violation API record and update it via external API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch_records(Request $request)
    {
        $id = $request->id;

        $record = ViolationApi::find($id);
        
        if(!$record){
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }

        $url   = $record->url;
        $field = $record->fetch_param; 
        
        // Date range (last 1 day)
        $day_start = Carbon::now()->subDays(3)->startOfDay()->format('Y-m-d\TH:i:s');
        $day_end   = Carbon::now()->subDays(2)->startOfDay()->format('Y-m-d\TH:i:s');

        // Where condition (dynamic column name)
        $condition = "$field between '$day_start' and '$day_end'";

        // Final API URL
        $api_url = $url . '?'. $condition;

        // Request
        $response = Http::get($api_url);

        if (!$response->successful()) {

            Log::error("API request failed: URL {$api_url} Status {$response->status()}");
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        
        }else{

            $user_id = auth()->id();
             
            try {                

                $data = $response->json();

                if(!empty($data)){

                    DB::beginTransaction();
                    foreach ($data as $item) {

                        $item['state'] = 'NY';
                        $validate_address = $this->validate_US_address($item);
                        $issue_date = Carbon::createFromFormat('Ymd', $item['issue_date'])->format('Y-m-d');
                        DB::table('violation_records')->insert([
                            'violation_type'    => $record->violation_type ?? 0,
                            'violation_api'     => $record->id ?? 0,
                            'issue_date'     => $issue_date ?? NULL,
                            'address1'          => ($item['house_number'].' '.$item['street']) ?? null,
                            'address2'          => $this->boro_map[$item['boro']] ?? null,                    
                            'state'             => $item['state'],                    
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
                
                }else{

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
    function validate_US_address(array $data)
    {

        $params = [
            "street" => ($data['house_number'] ?? '') . " " . ($data['street'] ?? ''),
            "city"   => $this->boro_map[$data['boro']] ?? '',
            "state"  => ($data['state'] ?? ''),
            "key"    => env('SMARTY_API_KEY') 
        ];

        $endpoint = "https://us-street.api.smarty.com/street-address";

        try {
            $response = Http::withHeaders([
                'Referer' =>  url('/')
            ])->get($endpoint, $params);
                        
            if ($response->successful()) {
                return !empty($response->json()) ? 1 : 2;
            }

            return 0; 
        } catch (\Exception $e) {
            Log::error("SmartyStreets API Error: " . $e->getMessage());
            return 0;
        }
    }  

    /**
     * Display a listing of the violation records.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_violation_records(ViolationRecordsDataTable $dataTable)
    {       
        $pageTitle = trans('global-message.list_form_title',['form' => trans('Violation Records')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '';
        return $dataTable->render('violation_records.list', compact('pageTitle','auth_user','assets', 'headerAction'));
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
        if(!$request->id){
            return response()->json(['error' => 'Invalid Request'], 500);
        }
        
        $record = DB::table('violation_records')->where('id',$request->id)->first(['address1','address2','state']);
        
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
                if(!empty($response->json())){
                    
                    $data['is_address_verify'] = 1;
                    $status = 200;
                    $message = 'Address successsfully validate';
                    $success = true;  

                }else{

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
}
