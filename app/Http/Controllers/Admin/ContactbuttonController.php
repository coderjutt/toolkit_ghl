<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CRM;
use App\Http\Controllers\Controller;
use App\Models\GhlAuth;
use Illuminate\Http\Request;

class ContactbuttonController extends Controller
{
    public function index()
    {
        return view('admin.modules.index');
    }
    public function getContact(){
       $allContacts = [];

        // DB se sabhi users ke tokens nikal lo
        $users = GhlAuth::where('user_type', CRM::$lang_loc)->get();

        foreach ($users as $user) {
            $company_id  = $user->user_id;
            $location_id = $user->location_id;

            // Contacts fetch karo
            $contacts = CRM::crmV2Loc(
                $company_id,
                $location_id,
                'contacts',
                'get'
            );

            $allContacts[$location_id] = $contacts;
        }
// dd($allContacts);
        return response()->json($allContacts);
    }

       public function getcalender(){
       $allContacts = [];

        // DB se sabhi users ke tokens nikal lo
        $users = GhlAuth::where('user_type', CRM::$lang_loc)->get();

        foreach ($users as $user) {
            $company_id  = $user->user_id;
            $location_id = $user->location_id;

            // Contacts fetch karo
            $contacts = CRM::crmV2Loc(
                $company_id,
                $location_id,
                'calendars',
                'get'
            );

            $allContacts[$location_id] = $contacts;
        }
// dd($allContacts);
        return response()->json($allContacts);
    }
     public function getcalenderdeta($location_id,$company_id,$calendarId ){
    //    $allContacts = [];

        // DB se sabhi users ke tokens nikal lo
        $users = GhlAuth::where('user_type', CRM::$lang_loc)->get();

        
            $company_id  = $users->user_id;
            $location_id = $users->location_id;

            // Contacts fetch karo
            $calendar = CRM::crmV2Loc(
                $company_id,
                $location_id,
                 "calendars/{$calendarId}",
                'get'
            );

            // $allContacts[$location_id] = $calendar;
        
// dd($allContacts);
        return response()->json($calendar);
    }


}
