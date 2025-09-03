<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomCss;
use App\Models\LocationCustomizer;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class LocationcustomizerController extends Controller
{
    public function index()
    {
        $locationcustomizer = LocationCustomizer::all();
         $userPermissions = UserPermission::where('user_id', auth()->id())
            ->get()
            ->groupBy('module')
            ->map(function ($rows) {
                return $rows->pluck('permission')->unique()->toArray();
            })
            ->toArray();
        return view('admin.locationcustomizer.index', compact('locationcustomizer','userPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|unique:location_customizer,location_id',
            'location' => 'required',
            'Enable' => 'required|boolean',
        ]);
        //  dd($request->all());
        $location = LocationCustomizer::create($request->only('location_id', 'location', 'Enable'));


        return redirect()->back()->with('success', 'Location added successfully.');
    }

    // Update location and related CustomCss
    public function update(Request $request, $id)
    {
        $location = LocationCustomizer::findOrFail($id);

        $request->validate([
            'location' => 'required',
            'Enable' => 'required|boolean',
        ]);

        $location->update($request->only('location', 'Enable'));

        return redirect()->back()->with('success', 'Location updated successfully.');
    }


    public function toggleEnable(Request $request)
    {
        $location = LocationCustomizer::findOrFail($request->id);
        $location->Enable = $request->enable;
        $location->save();

        return response()->json(['success' => true]);
    }



    public function updateCSS(Request $request, $email, $locationId, $ghl_location_id)
    {
        // Step 1: Find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Step 2: Find location
        $location = LocationCustomizer::where('id', $locationId)
            ->where('location_id', $ghl_location_id)
            ->first();

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        // Step 3: Validate request fields
        $validated = $request->validate([
            'card_header_background' => 'nullable|string',
            'card_header_color' => 'nullable|string',
            'top_header_icon_background' => 'nullable|string',
            'top_header_icon_color' => 'nullable|string',
            'navebar_background' => 'nullable|string',
            'navebar_color' => 'nullable|string',
            'navebar_grouped_background' => 'nullable|string',
            'navebar_grouped_color' => 'nullable|string',
            'navebar_item_active_background' => 'nullable|string',
            'navebar_item_active_color' => 'nullable|string',
            'navebar_item_inactive_background' => 'nullable|string',
            'navebar_item_inactive_color' => 'nullable|string',
            'navebar_image_color' => 'nullable|string',
            'navebar_image_hover' => 'nullable|string',
            'item_border_radius' => 'nullable|string',
            'live_privew' => 'nullable|string',
            'custom_css' => 'nullable|string',
        ]);

        // Step 4: Update or create CSS record
        $customCss = CustomCss::updateOrCreate(
            ['location_customizer_id' => $location->id],
            array_merge($validated, [
                'live_preview' => $request->input('live_preview', 0) // default 0
            ])
        );
        return response()->json(['success' => true, 'data' => $customCss]);
    }

    public function getApiLocationCustomizer(Request $request, $email)
    {
        // Step 1: User check
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Step 2: Agar toggle ke liye data aya hai to update karo
        if ($request->filled('id') && $request->filled('enable')) {
            $location = LocationCustomizer::find($request->id);

            if (!$location) {
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            $location->Enable = $request->enable;
            $location->save();
        }

        // Step 3: Saare LocationCustomizer records fetch karo
        $locationCustomizer = LocationCustomizer::all();

        // Step 4: Response return karo
        return response()->json([
            'success' => true,
            'data' => $locationCustomizer
        ]);
    }
    public function getCSS($email, $locationId, $ghl_location_id)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $location = LocationCustomizer::where('id', $locationId)
            ->where('location_id', $ghl_location_id)
            ->first();

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $customCss = CustomCss::where('location_customizer_id', $location->id)->first();

        return response()->json([
            'success' => true,
            'live_preview' => $customCss->live_preview ?? 0,
            'data' => $customCss
        ]);
    }


}

