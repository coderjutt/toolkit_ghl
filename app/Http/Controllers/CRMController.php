<?php

namespace App\Http\Controllers;

use App\Helpers\CRM;
use Illuminate\Http\Request;
use App\Models\GhlAuth;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\LocationDetail;
class CRMController extends Controller
{
    public function crmCallback(Request $request)
    {
        $code = $request->code ?? null;
        // dd($code);
        if ($code) {
            $user_id = null;
            if (auth()->check()) {
                $user = loginUser(); //auth user
                // if ($user->role == company_role()) {

                // }
                $user_id = $user->id;
            }
            $code = CRM::crm_token($code, '');
            // dd(vars: $code);
            $code = json_decode($code);
            $user_type = $code->userType ?? null;
            // dd($user_type,$user_id);
            $main = route('admin.setting.index'); //change with any desired
            if ($user_type) {
                $token = $user->ghlauth ?? null;

                list($connected, $con) = CRM::go_and_get_token($code, '', $user_id, $token);
                //dd($con);
                if ($connected) {
                    return redirect($main)->with('success', 'Connected Successfully');
                }
                return redirect($main)->with('error', json_encode($code));
            }
            return response()->json(['message' => 'Not allowed to connect']);
        }
    }


    public function fetchLocations(Request $request)
    {
        //this code is only useable if need to store locations in database or connect with already saved locations in database using agency token
        $user = loginUser();
        $token1 =  $user->ghlauth ?? null;
        $token=$token1[0];
        //dd($token);
        $status = false;
        $message = 'Connect to  Agency First';
        $type = '';
        $detail = '';
        $load_more = false;
        if ($token) {

            $type = $token->user_type;

            $query = '';
            $limit = 100;
            if ($request->has('page')) {
                if ($request->page < 2) {
                    $request->page = 0;
                }
                $query .= 'skip=' . ($limit * $request->page) . '&';
            }
            $query = 'locations/search?' . $query . 'limit=' . $limit . '&companyId=' . $token->company_id;

            if ($type !== \CRM::$lang_com) {
                return response()->json(['status' => $status, 'message' => $message, 'type' => $type, 'detail' => $detail, 'loadMore' => $load_more]);
            } else {
               // dd("404");
                $detail = \CRM::agencyV2($user->id, $query, 'get', '', [], false, $token);
                //dd($detail);
            }

            if ($detail && property_exists($detail, 'locations')) {
                $detail = $detail->locations;
                $load_more = count($detail) > $limit - 1;
                $ids = collect($detail)->pluck('id')->toArray();
                $locs_already = []; // Locations::whereIn('location_id', $ids)->pluck('location_id')->toArray();
                foreach ($detail as $det) {
                    \Log::info(['crm' => $det]);
                    $locationId= \CRM::connectLocation($user->id,$det->id,$token);
                   // dd($locationId);
                    if(!empty($locationId)  && isset($locationId->location_id)){
                        $ghl = GhlAuth::where('location_id', $locationId->location_id)->first();
                        if ($ghl) {
                            $ghl->name = $det->name;
                            $ghl->save();
                            \Log::info('Updated GhlAuth record', [
                                'location_id' => $locationId->location_id,
                                'name' => $det->name,
                            ]);
                        }
                        }
                    if (!in_array($det->id, $locs_already)) {
                        //saveLocs($det, $user->id);
                    }
                }
                $status = true;
            }

        }
        return response()->json(['status' => $status, 'message' => $message, 'type' => $type, 'detail' => $detail, 'loadMore' => $load_more]);
    }
    public function synLocationData()
{
    $token = GhlAuth::where('user_id', login_id())->first();

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Token not found.'
        ], 404);
    }

    $locationId = $token->location_id;
    $userId = login_id();

    // Sync Location Details
    $locationData = \CRM::crmV2($userId, "locations/{$locationId}", 'get', '', [], false, $locationId, $token);
    if (!isset($locationData->location)) {
        return response()->json([
            'status' => false,
            'message' => 'Location data not found.'
        ], 404);
    }

    $location = $locationData->location;

    LocationDetail::updateOrCreate(
        ['location_id' => $location->id],
        [
            'company_id' => $location->companyId ?? null,
            'name' => $location->name ?? null,
            'address' => $location->address ?? null,
            'city' => $location->city ?? null,
            'state' => $location->state ?? null,
            'country' => $location->country ?? null,
            'postal_code' => $location->postalCode ?? null,
            'website' => $location->website ?? null,
            'timezone' => $location->timezone ?? null,
            'first_name' => $location->firstName ?? null,
            'last_name' => $location->lastName ?? null,
            'email' => $location->email ?? null,
            'phone' => $location->phone ?? null,
            'logo_url' => $location->logoUrl ?? null,
            'domain' => $location->domain ?? null,
            'business' => isset($location->business) ? json_encode($location->business) : null,
            'business_logo_url' => $location->business->logoUrl ?? null,
            'social' => isset($location->social) ? json_encode($location->social) : null,
            'settings' => isset($location->settings) ? json_encode($location->settings) : null,
            'user_id' => login_id(),
        ]
    );

    // Sync Users
    $ghlUsers = \CRM::crmV2($userId, 'users', 'get', '', [], false, $locationId, $token);
    $userSynced = false;

    if (!empty($ghlUsers->users)) {
        foreach ($ghlUsers->users as $user) {
            ghlUser($user); // assumes ghlUser handles persistence
        }
        $userSynced = true;
    }

    // Sync Pipelines and Stages
    $pipelinesData = \CRM::crmV2($userId, 'opportunities/pipelines', 'get', '', [], false, $locationId, $token);

    if (!empty($pipelinesData->pipelines)) {
        foreach ($pipelinesData->pipelines as $apiPipeline) {
            $pipeline = Pipeline::updateOrCreate(
                [
                    'pipeline_id' => $apiPipeline->id,
                    'location_id' => $locationId,
                ],
                [
                    'name' => $apiPipeline->name,
                ]
            );

            foreach ($apiPipeline->stages as $stage) {
                PipelineStage::updateOrCreate(
                    [
                        'pipeline_id' => $pipeline->id,
                        'location_id' => $locationId,
                        'pipeline_stage_id' => $stage->id,
                    ],
                    [
                        'name' => $stage->name,
                        'position' => $stage->position,
                    ]
                );
            }
        }
    }

    return response()->json([
        'status' => true,
        'message' => $userSynced ? 'Users synced successfully.' : 'Location and pipelines synced, but no users found.',
    ]);
}

}
