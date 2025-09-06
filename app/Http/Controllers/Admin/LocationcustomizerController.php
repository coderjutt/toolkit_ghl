<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomCss;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\LocationCustomizer;
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
        return view('admin.locationcustomizer.index', compact('locationcustomizer', 'userPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|unique:location_customizer,location_id',
            'location' => 'required',
            'Enable' => 'required|boolean',
        ]);

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


    public function destroy($id)
    {
        $location = LocationCustomizer::findOrFail($id);
        $location->delete();
        return redirect()->back()->with('success', 'Delete successfully');
    }

    public function toggleEnable(Request $request)
    {
        $location = LocationCustomizer::findOrFail($request->id);
        $location->Enable = $request->enable;
        $location->save();

        return response()->json(['success' => true]);
    }

    public function editCSS($locationId)
    {
        $location = LocationCustomizer::where('id', $locationId)->firstOrFail();

        $customCss = CustomCss::where('location_customizer_id', $location->id)->first();

        $data = $customCss ? $customCss->toArray() : [
            'card_header_background' => '#ffffff',
            'card_header_color' => '#000000',
            'top_header_icon_background' => '#ffffff',
            'top_header_icon_color' => '#000000',
            'navebar_background' => '#ffffff',
            'navebar_color' => '#000000',
            'navebar_grouped_background' => '#ffffff',
            'navebar_grouped_color' => '#000000',
            'navebar_item_active_background' => '#ffffff',
            'navebar_item_active_color' => '#000000',
            'navebar_item_inactive_background' => '#ffffff',
            'navebar_item_inactive_color' => '#000000',
            'navebar_image_color' => '#000000',
            'navebar_image_hover' => '#000000',
            'item_border_radius' => 0,
            'live_privew' => 0,
            'custom_css' => '',
        ];

        return response()->json($data);
    }

    // Save or update CSS
    public function updateCSS(Request $request, $locationId)
    {
        $location = LocationCustomizer::where('id', $locationId)->firstOrFail();

        // custom_css string alag save hoga
        $customCssValue = $request->input('custom_css');

        // baaki sab fields JSON me convert kar ke save karenge
        $jsonFields = $request->except(['custom_css', '_token']);
        $formattedNewFields = [];
        foreach ($jsonFields as $key => $value) {
            $formattedNewFields[$key] = is_array($value) ? $value : [$value];
        }
        $customCss = CustomCss::updateOrCreate(
            ['location_customizer_id' => $location->id],
            [
                'custom_css' => $customCssValue,
                'form_css' => $formattedNewFields
            ]
        );

        return response()->json(['success' => true]);
    }



    //  public function updateCSSffd(Request $request, $email, $locationId, $ghl_location_id)
    // {
    //     // Step 1: Find user by email
    //     $user = User::where('email', $email)->first();
    //     if (!$user) {
    //         return response()->json(['success' => false, 'message' => 'User not found'], 404);
    //     }

    //     // Step 2: Find location
    //     $location = LocationCustomizer::where('id', $locationId)
    //         ->where('location_id', $ghl_location_id)
    //         ->first();

    //     if (!$location) {
    //         return response()->json(['success' => false, 'message' => 'Location not found'], 404);
    //     }

    //     // Step 3: Validate request fields
    //     $validated = $request->validate([
    //         'card_header_background' => 'nullable|string',
    //         'card_header_color' => 'nullable|string',
    //         'top_header_icon_background' => 'nullable|string',
    //         'top_header_icon_color' => 'nullable|string',
    //         'navebar_background' => 'nullable|string',
    //         'navebar_color' => 'nullable|string',
    //         'navebar_grouped_background' => 'nullable|string',
    //         'navebar_grouped_color' => 'nullable|string',
    //         'navebar_item_active_background' => 'nullable|string',
    //         'navebar_item_active_color' => 'nullable|string',
    //         'navebar_item_inactive_background' => 'nullable|string',
    //         'navebar_item_inactive_color' => 'nullable|string',
    //         'navebar_image_color' => 'nullable|string',
    //         'navebar_image_hover' => 'nullable|string',
    //         'item_border_radius' => 'nullable|string',
    //         'live_privew' => 'nullable|string',
    //         'custom_css' => 'nullable|string',
    //     ]);

    //     $customCss = CustomCss::updateOrCreate(
    //         ['location_customizer_id' => $location->id],
    //         array_merge($validated, [
    //             'live_preview' => $request->input('live_privew', 0) // default 0
    //         ])
    //     );

    //     return response()->json(['success' => true, 'data' => $customCss]);
    // }


    // public function updateCSSapi(Request $request, $email, $locationId, $ghl_location_id)
    // {
    //     // Step 1: Find user by email
    //     $user = User::where('email', $email)->first();
    //     if (!$user) {
    //         return response()->json(['success' => false, 'message' => 'User not found'], 404);
    //     }

    //     // Step 2: Find location
    //     $location = LocationCustomizer::where('id', $locationId)
    //         ->where('location_id', $ghl_location_id)
    //         ->first();

    //     if (!$location) {
    //         return response()->json(['success' => false, 'message' => 'Location not found'], 404);
    //     }

    //     // Step 3: Split custom_css and other fields
    //     $customCssValue = $request->input('custom_css', ''); // default empty string
    //     $jsonFields = $request->except(['custom_css', '_token']); // sab dynamic fields

    //     // Har value ko array bana do
    //     $formattedNewFields = [];
    //     foreach ($jsonFields as $key => $value) {
    //         $formattedNewFields[$key] = is_array($value) ? $value : [$value];
    //     }

    //     // Step 4: Merge with old data (if exists)
    //     $existingCss = CustomCss::where('location_customizer_id', $location->id)->first();
    //     $existingFields = $existingCss ? ($existingCss->form_css ?? []) : [];

    //     // Merge old + new (overwrite if key exists)
    //     $mergedFields = array_merge($existingFields, $formattedNewFields);

    //     // Step 5: Save into DB
    //     $customCss = CustomCss::updateOrCreate(
    //         ['location_customizer_id' => $location->id],
    //         [
    //             'custom_css' => $customCssValue,
    //             'form_css' => $mergedFields
    //         ]
    //     );

    //     return response()->json([
    //         'success' => true,
    //         'data' => $customCss
    //     ]);
    // }

    public function updateCSSapi(Request $request, $email, $locationId, $ghl_location_id)
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

        // Step 3: Raw JSON body ya fallback to normal request
        $rawInput = $request->json()->all();
        if (empty($rawInput)) {
            $rawInput = $request->all(); // agar query string / form-data hai
        }

        if (empty($rawInput)) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON or empty request'], 400);
        }

        // Step 4: Split custom_css aur baaki fields
        $customCssValue = $rawInput['custom_css'] ?? '';
        unset($rawInput['custom_css'], $rawInput['_token']);

        // Har value ko array bana do
        $formattedNewFields = [];
        foreach ($rawInput as $key => $value) {
            $formattedNewFields[$key] = is_array($value) ? $value : [$value];
        }

        // Step 5: Merge with existing
        $existingCss = CustomCss::where('location_customizer_id', $location->id)->first();
        $existingFields = $existingCss ? ($existingCss->form_css ?? []) : [];

        $mergedFields = array_merge($existingFields, $formattedNewFields);

        // Step 6: Save into DB
        $customCss = CustomCss::updateOrCreate(
            ['location_customizer_id' => $location->id],
            [
                'custom_css' => $customCssValue,
                'form_css'   => $mergedFields
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => $customCss
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

    public function getApiLocationCustomizer(Request $request, $email)
    {
        // Step 1: User check
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Step 2: Agar toggle ke liye data aya hai to update karo
        $enableValue = $request->input('enable') ?? $request->input('Enable'); // case-insensitive handle
        if ($request->filled('id') && $enableValue !== null) {
            $location = LocationCustomizer::find($request->id);

            if (!$location) {
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            $location->Enable = $enableValue;
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


}

