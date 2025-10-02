<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CRM;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementEmailSetting;
use App\Models\AnnouncementSetting;
use App\Models\GhlAuth;
use App\Models\GhlUser;
use App\Models\GhlUser2;
use App\Models\User;
use App\Models\UserPermission;
use DB;
use App\Jobs\SendGhlAnnouncement;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;



class AnnouncementController extends Controller
{


    public function index()
    {
        $announcements = Announcement::where('user_id', login_id())->paginate(10);

        $announcements->getCollection()->transform(function ($announcement) {
            // Agar DB already array return kar raha hai to direct use karo
            $locations = is_array($announcement->locations)
                ? $announcement->locations
                : json_decode($announcement->locations, true);

            $announcement->emails = $locations['email'] ?? [];
            $announcement->location_ids = $locations['location_id'] ?? [];

            // Settings attach
            $settings = AnnouncementSetting::where('user_id', login_id())
                ->whereJsonContains('settings->announcement_id', (string) $announcement->id)
                ->first();

            $announcement->currentSettings = $settings ? $settings->settings : [
                'audience' => ['types' => []],
                'conditions' => ['stop' => 'never', 'views' => 1],
                'frequency' => ['mode' => 'every_page', 'value' => 1, 'unit' => 'days'],
            ];

            return $announcement;
        });

        $settings = AnnouncementSetting::where('user_id', login_id())
            ->where(function ($q) {
                $q->whereNull('settings->announcement_id')
                    ->orWhere('settings->announcement_id', 'null'); // JSON me string "null"
            })
            ->first();

        $userPermissions = UserPermission::where('user_id', auth()->id())
            ->get()
            ->groupBy('module')
            ->map(function ($rows) {
                return $rows->pluck('permission')->unique()->toArray();
            })
            ->toArray();

        return view('admin.announcement.index', compact(
            'announcements',
            'settings',
            'userPermissions',
        ));
    }


    public function create()
    {
        $locationIds = GhlAuth::where('user_type', 'Location')
            ->where('user_id', auth()->id())
            ->pluck('location_id');
        $users = User::select('id', 'email')->get();

        $roles = $users->pluck('role')->filter()->unique()->values();

        return view('admin.announcement.create', compact('locationIds', 'users', 'roles'));
    }



    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'status' => 'required|string',
    //         'expiry_type' => 'required|string',
    //         'expiry_date' => 'nullable|date',
    //         'audience_type' => 'required|string', // e.g. Location, Company, Agency
    //         'title' => 'required|string',
    //         'body' => 'required|string',
    //         'display_setting' => 'required|string',
    //         'allow_email' => 'nullable|boolean',
    //         'send_email' => 'nullable|boolean',
    //         'users' => 'nullable|array',
    //     ]);
    //     // dd($data);
    //     $announcement = new Announcement();
    //     $announcement->status = $data['status'];
    //     $announcement->expiry_type = $data['expiry_type'];
    //     $announcement->expiry_date = $data['expiry_type'] === 'date' ? $data['expiry_date'] : null;

    //     // 🔹 audience type direct save
    //     $announcement->audience_type = $data['audience_type'];

    //     $announcement->title = $data['title'];
    //     $announcement->body = $data['body'];
    //     $announcement->display_setting = $data['display_setting'];
    //     $announcement->allow_email = $request->boolean('allow_email');
    //     $announcement->send_email = $request->boolean('send_email');
    //     $announcement->user_id = auth()->id();

    //     $locationsData = [];

    //     // 🔹 Specific audience => save users array
    //     if ($data['audience_type'] === 'specific' && $request->filled('users')) {
    //         $users = GhlUser2::whereIn('ghl_user_id', $request->users)
    //             ->get(['ghl_user_id', 'location_id']);

    //         foreach ($users as $user) {
    //             $locationsData[] = [
    //                 'ghl_user_id' => $user->ghl_user_id,
    //                 'location_id' => $user->location_id,
    //             ];
    //         }
    //     } else {
    //         // 🔹 All audience => creator ka location save
    //         $ghlAuth = GhlAuth::where('user_id', auth()->id())
    //             ->where('user_type', 'Location')
    //             ->first();

    //         if ($ghlAuth) {
    //             $locationsData[] = [
    //                 'ghl_user_id' => null,
    //                 'location_id' => $ghlAuth->location_id,
    //             ];
    //         }
    //     }

    //     // 🔹 Save JSON exactly in desired format
    //     $announcement->locations = json_encode($locationsData, JSON_PRETTY_PRINT);

    //     $announcement->save();

    //     return redirect()->route('admin.announcement.index')
    //         ->with('success', 'Announcement created successfully.');
    // }


    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'status' => 'required|string',
    //         'expiry_type' => 'required|string',
    //         'expiry_date' => 'nullable|date',
    //         'audience_type' => 'required|string',
    //         'title' => 'required|string',
    //         'custom_views' => 'nullable|integer|min:1',
    //         'body' => 'required|string',
    //         'display_setting' => 'required|string',
    //         'allow_email' => 'nullable|boolean',
    //         'send_email' => 'nullable|boolean',
    //         'emails' => 'nullable|array',
    //         'location_ids' => 'nullable|array',
    //     ]);
    //     // dd($request->all());
    //     $announcement = new Announcement();
    //     $announcement->status = $data['status'];
    //     $announcement->expiry_type = $data['expiry_type'];
    //     $announcement->expiry_date = $data['expiry_type'] === 'date' ? $data['expiry_date'] : null;
    //     $announcement->audience_type = $data['audience_type'];
    //     $announcement->title = $data['title'];
    //     $announcement->body = $data['body'];
    //     // $announcement->display_setting = $data['display_setting'];

    //     // handle display_setting
    //     if ($data['display_setting'] === 'custom' && !empty($data['custom_views'])) {
    //         $announcement->display_setting = 'stop_after_' . $data['custom_views'] . '_view';
    //     } else {
    //         $announcement->display_setting = $data['display_setting'];
    //     }

    //     $announcement->allow_email = $request->boolean('allow_email');
    //     $announcement->send_email = $request->boolean('send_email');
    //     $announcement->user_id = auth()->id();

    //     $locationsData = [];

    //     if ($data['audience_type'] === 'specific') {
    //         $locationIdsArray = $request->location_ids ?? [];
    //         $emailsArray = $request->emails ?? [];

    //         // decode first element (kyunki har input ek JSON string array ke form me aa raha hai)
    //         $decodedLocations = [];
    //         $decodedEmails = [];

    //         if (!empty($locationIdsArray[0])) {
    //             $decodedLocations = json_decode($locationIdsArray[0], true) ?? [];
    //         }

    //         if (!empty($emailsArray[0])) {
    //             $decodedEmails = json_decode($emailsArray[0], true) ?? [];
    //         }

    //         // max loop chalega jitne bhi records zyada hain
    //         $maxCount = max(count($decodedLocations), count($decodedEmails));

    //         for ($i = 0; $i < $maxCount; $i++) {
    //             $locationsData[] = [
    //                 'email' => $decodedEmails[$i]['value'] ?? null,
    //                 'location_id' => $decodedLocations[$i]['value'] ?? null,
    //             ];
    //         }
    //     } else {
    //         $user = auth()->user();

    //         if ($user) {
    //             $locationsData[] = [
    //                 null
    //             ];
    //         }
    //     }

    //     // ✅ Save JSON clean
    //     $announcement->locations = $locationsData;
    //     $announcement->save();

    //     return redirect()->route('admin.announcement.index')
    //         ->with('success', 'Announcement created successfully.');
    // }



    // public function update(Request $request, $id)
    // {
    //     $data = $request->validate([
    //         'status' => 'required|string',
    //         'expiry_type' => 'required|string',
    //         'expiry_date' => 'nullable|date',
    //         'audience_type' => 'required|string', // e.g. Location, Company, Agency, Specific
    //         'title' => 'required|string',
    //         'body' => 'required|string',
    //         'display_setting' => 'required|string',
    //         'allow_email' => 'nullable|boolean',
    //         'send_email' => 'nullable|boolean',
    //         'users' => 'nullable|array',
    //     ]);

    //     $announcement = Announcement::findOrFail($id);

    //     $announcement->status = $data['status'];
    //     $announcement->expiry_type = $data['expiry_type'];
    //     $announcement->expiry_date = $data['expiry_type'] === 'date' ? $data['expiry_date'] : null;

    //     // Audience type
    //     $announcement->audience_type = $data['audience_type'];

    //     $announcement->title = $data['title'];
    //     $announcement->body = $data['body'];
    //     $announcement->display_setting = $data['display_setting'];
    //     $announcement->allow_email = $request->boolean('allow_email');
    //     $announcement->send_email = $request->boolean('send_email');

    //     $locationsData = [];

    //     // 🔹 If specific audience => save selected users
    //     if ($data['audience_type'] === 'specific' && $request->filled('users')) {
    //         $users = GhlUser2::whereIn('ghl_user_id', $request->users)
    //             ->get(['ghl_user_id', 'location_id']);

    //         foreach ($users as $user) {
    //             $locationsData[] = [
    //                 'ghl_user_id' => $user->ghl_user_id,
    //                 'location_id' => $user->location_id,
    //             ];
    //         }
    //     } else {
    //         // 🔹 Otherwise => save creator’s location
    //         $ghlAuth = GhlAuth::where('user_id', auth()->id())
    //             ->where('user_type', 'Location')
    //             ->first();

    //         if ($ghlAuth) {
    //             $locationsData[] = [
    //                 'ghl_user_id' => null,
    //                 'location_id' => $ghlAuth->location_id,
    //             ];
    //         }
    //     }

    //     // 🔹 Save JSON directly (DB column is JSON type)
    //     $announcement->locations = $locationsData;

    //     $announcement->save();

    //     return redirect()->route('admin.announcement.index')
    //         ->with('success', 'Announcement updated successfully.');
    // }



    public function store(Request $request)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'expiry_type' => 'required|string',
            'expiry_date' => 'nullable|date',
            'audience_type' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'display_setting' => 'required|string',
            'custom_views' => 'nullable|integer|min:1',
            'allow_email' => 'nullable|boolean',
            'send_email' => 'nullable|boolean',
            'emails' => 'nullable|array',
            'location_ids' => 'nullable|array',
            'audience' => 'nullable|array',
            'stop' => 'nullable|string',
            'views' => 'nullable|integer',
            'freq' => 'nullable|string',
            'freq_value' => 'nullable|integer',
            'freq_unit' => 'nullable|string',
        ]);

        $announcement = new Announcement();
        // if ($announcement->user_id !== auth()->id()) {
        //     return redirect()->back()->with('error', 'Unauthorized action.');
        // }
        $announcement->status = $data['status'];
        $announcement->expiry_type = $data['expiry_type'];
        $announcement->expiry_date = $data['expiry_type'] === 'date' ? $data['expiry_date'] : null;
        $announcement->audience_type = $data['audience_type'];
        $announcement->title = $data['title'];
        $announcement->body = $data['body'];
        $announcement->allow_email = $request->boolean('allow_email');
        $announcement->send_email = $request->boolean('send_email');
        $announcement->user_id = auth()->id();

        // Handle display setting

        if ($data['display_setting'] === 'custom' && !empty($data['custom_views'])) {
            $announcement->display_setting = 'stop_after_' . $data['custom_views'] . '_view';
        } else {
            $announcement->display_setting = $data['display_setting'];
        }

        $useGeneralSettings = $request->boolean('use_general_settings');

        // Build settings array
        $settings = [];

        if ($useGeneralSettings) {
            // If general settings is used, mark it as general
            $settings['general_settings'] = true;
        } else {
            // Otherwise, save custom audience and frequency
            $settings['general_settings'] = false;
            $settings['audience_types'] = $data['audience']['types'] ?? [];
            $settings['frequency'] = [
                'type' => $data['freq'] ?? 'every_page',
                'value' => $data['freq_value'] ?? null,
                'unit' => $data['freq_unit'] ?? null,
            ];
        }

        $announcement->settings = $settings;

        // Handle specific users / locations
        $locationsData = [];
        if ($data['audience_type'] === 'specific') {
            $locationIdsArray = $request->location_ids ?? [];
            $emailsArray = $request->emails ?? [];

            // decode first element (kyunki har input ek JSON string array ke form me aa raha hai)
            $decodedLocations = [];
            $decodedEmails = [];

            if (!empty($locationIdsArray[0])) {
                $decodedLocations = json_decode($locationIdsArray[0], true) ?? [];
            }

            if (!empty($emailsArray[0])) {
                $decodedEmails = json_decode($emailsArray[0], true) ?? [];
            }

            // max loop chalega jitne bhi records zyada hain
            $maxCount = max(count($decodedLocations), count($decodedEmails));

            for ($i = 0; $i < $maxCount; $i++) {
                $locationsData[] = [
                    'email' => $decodedEmails[$i]['value'] ?? null,
                    'location_id' => $decodedLocations[$i]['value'] ?? null,
                ];
            }
        } else {
            $user = auth()->user();

            if ($user) {
                $locationsData[] = [
                    null
                ];
            }
        }
        $announcement->locations = $locationsData;
        $announcement->save();

        return redirect()->route('admin.announcement.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'expiry_type' => 'required|string',
            'expiry_date' => 'nullable|date',
            'audience_type' => 'required|string', // all/specific
            'custom_views' => 'nullable|integer|min:1',
            'title' => 'required|string',
            'body' => 'required|string',
            'display_setting' => 'required|string',
            'allow_email' => 'nullable|boolean',
            'send_email' => 'nullable|boolean',
            'audience' => 'nullable|array',
            'stop' => 'nullable|string',
            'views' => 'nullable|integer',
            'freq' => 'nullable|string',
            'freq_value' => 'nullable|integer',
            'freq_unit' => 'nullable|string',
        ]);

        $announcement = Announcement::findOrFail($id);

        $announcement->status = $data['status'];
        $announcement->expiry_type = $data['expiry_type'];
        $announcement->expiry_date = $data['expiry_type'] === 'date' ? $data['expiry_date'] : null;
        $announcement->audience_type = $data['audience_type'];
        $announcement->title = $data['title'];
        $announcement->body = $data['body'];
        // $announcement->display_setting = $data['display_setting'];
        if ($data['display_setting'] === 'custom' && !empty($data['custom_views'])) {
            $announcement->display_setting = 'stop_after_' . $data['custom_views'] . '_view';
        } else {
            $announcement->display_setting = $data['display_setting'];
        }

        $useGeneralSettings = $request->boolean('use_general_settings');

        // Build settings array
        $settings = [];

        if ($useGeneralSettings) {
            // If general settings is used, mark it as general
            $settings['general_settings'] = true;
        } else {
            // Otherwise, save custom audience and frequency
            $settings['general_settings'] = false;
            $settings['audience_types'] = $data['audience']['types'] ?? [];
            $settings['frequency'] = [
                'type' => $data['freq'] ?? 'every_page',
                'value' => $data['freq_value'] ?? null,
                'unit' => $data['freq_unit'] ?? null,
            ];
        }

        $announcement->settings = $settings;
        $announcement->allow_email = $request->boolean('allow_email');
        $announcement->send_email = $request->boolean('send_email');

        $locationsData = [];

        if ($data['audience_type'] === 'specific') {
            $locationIds = $request->input('location_ids', []);
            $emails = $request->input('emails', []);

            $maxCount = max(count($locationIds), count($emails));

            for ($i = 0; $i < $maxCount; $i++) {
                $locationsData[] = [
                    'location_id' => $locationIds[$i] ?? null,
                    'email' => $emails[$i] ?? null,
                ];
            }
        } else {
            $user = auth()->user();

            if ($user) {
                // $locationsData[] = [
                //     'user_id' => $user->id,
                //     'email' => $user->email,
                // ];
                [null];
            }
        }


        // Save as JSON
        $announcement->locations = $locationsData;
        $announcement->save();

        return redirect()->route('admin.announcement.index')
            ->with('success', 'Announcement updated successfully.');
    }


    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        if ($announcement->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        $announcement->delete();
        return redirect()->back()->with('success', 'Announcement delete successfully');
    }


    public function getUsers(Request $request, $locationId)
    {
        // DB se token nikalna
        $auth = GhlAuth::where('location_id', $locationId)->first();

        if (!$auth) {
            return response()->json(['error' => 'No auth record found for this location'], 404);
        }

        $accessToken = $auth->access_token;
        $locationId = $auth->location_id;

        try {
            $client = new Client();
            $url = "https://services.leadconnectorhq.com/users/";

            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Version' => '2021-07-28',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'locationId' => $locationId
                ]
            ]);

            $users = json_decode($response->getBody(), true);

            return response()->json($users);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function syncUsers($locationId)
    {
        // DB se auth uthao
        $auth = GhlAuth::where('location_id', $locationId)->first();
        if (!$auth) {
            return response()->json(['error' => 'No auth found for this location'], 404);
        }

        $accessToken = $auth->access_token;

        try {
            $client = new Client();
            $url = "https://services.leadconnectorhq.com/users/";

            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Version' => '2021-07-28',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'locationId' => $locationId
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['users'])) {
                return response()->json(['error' => 'No users found in API response'], 400);
            }
            foreach ($data['users'] as $user) {
                GhlUser2::updateOrCreate(
                    [
                        'ghl_user_id' => $user['id'],
                        'location_id' => $locationId
                    ],
                    [
                        'company_id' => $auth->company_id ?? null,
                        'first_name' => $user['firstName'] ?? null,
                        'last_name' => $user['lastName'] ?? null,
                        'email' => $user['email'] ?? null,
                        'password' => null,
                        'phone' => isset($user['lcPhone'][$locationId]) ? $user['lcPhone'][$locationId] : null,
                        'type' => $user['roles']['type'] ?? 'user',   // ✅ correct
                        'role' => $user['roles']['role'] ?? 'member', // ✅ correct
                        'permissions' => $user['permissions'] ?? [],
                        'scopes' => $user['scopes'] ?? [],
                        'scopes_assigned_to_only' => $user['scopesAssignedToOnly'] ?? [],
                        'user_id' => null,
                        'profile_photo' => $user['profilePhoto'] ?? null,
                    ]
                );
            }

            return response()->json([
                'message' => 'Users synced successfully',
                'count' => count($data['users']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function save_settings(Request $request)
    // {
    //     $request->validate([
    //         'announcement_id' => 'nullable|integer|exists:announcements,id',
    //         'audience.types' => 'array|nullable',
    //         'stop' => 'nullable|string',
    //         'views' => 'nullable|integer',
    //         'freq' => 'nullable|string',
    //         'freq_value' => 'nullable|integer',
    //         'freq_unit' => 'nullable|string',
    //     ]);

    //     if ($request->filled('announcement_id')) {
    //         // 🔹 Agar specific announcement ke liye setting hai
    //         $settings = AnnouncementSetting::updateOrCreate(
    //             ['id' => $request->announcement_id], // 👈 isko id ki tarah treat kar lo
    //             [
    //                 'settings' => [
    //                     'announcement_id' => $request->announcement_id, // 👈 JSON me save
    //                     'audience' => [
    //                         'types' => $request->input('audience.types', []),
    //                     ],
    //                     'conditions' => [
    //                         'stop' => $request->stop ?? null,
    //                         'views' => $request->views ?? null,
    //                     ],
    //                     'frequency' => [
    //                         'mode' => $request->freq ?? null,
    //                         'value' => $request->freq_value ?? 1,
    //                         'unit' => $request->freq_unit ?? 'days',
    //                     ],
    //                 ]
    //             ]
    //         );
    //     } else {
    //         // 🔹 Global setting
    //         $settings = AnnouncementSetting::updateOrCreate(
    //             ['id' => 2], // ek fixed global record
    //             [
    //                 'settings' => [
    //                     'announcement_id' => null, // global me null
    //                     'audience' => [
    //                         'types' => $request->input('audience.types', []),
    //                     ],
    //                     'conditions' => [
    //                         'stop' => $request->stop ?? null,
    //                         'views' => $request->views ?? null,
    //                     ],
    //                     'frequency' => [
    //                         'mode' => $request->freq ?? null,
    //                         'value' => $request->freq_value ?? 1,
    //                         'unit' => $request->freq_unit ?? 'days',
    //                     ],
    //                 ]
    //             ]
    //         );
    //     }

    //     return redirect()->route('admin.announcement.index')
    //         ->with('success', 'Settings saved successfully!');
    // }


    public function save_settings(Request $request)
    {
        $request->validate([
            'announcement_id' => 'nullable|integer|exists:announcements,id',
            'audience.types' => 'array|nullable',
            'stop' => 'nullable|string',
            'views' => 'nullable|integer',
            'freq' => 'nullable|string',
            'freq_value' => 'nullable|integer',
            'freq_unit' => 'nullable|string',
        ]);

        $userId = auth()->id(); // 👈 current logged in user

        $data = [
            'settings' => [
                'announcement_id' => $request->announcement_id ?? null,
                'audience' => [
                    'types' => $request->input('audience.types', []),
                ],
                'conditions' => [
                    'stop' => $request->stop ?? null,
                    'views' => $request->views ?? null,
                ],
                'frequency' => [
                    'mode' => $request->freq ?? null,
                    'value' => $request->freq_value ?? 1,
                    'unit' => $request->freq_unit ?? 'days',
                ],
            ]
        ];

        // 🔹 Update or create per-user settings
        $settings = AnnouncementSetting::updateOrCreate(
            ['user_id' => $userId], // 👈 unique by user_id
            $data
        );

        return redirect()->route('admin.announcement.index')
            ->with('success', 'Settings saved successfully!');
    }




    public function save_settingswithrow(Request $request)
    {
        $announcementId = $request->announcement_id;

        $settingsData = [
            'announcement_id' => $announcementId,
            'audience' => [
                'types' => $request->input('audience.types', [])
            ],
            'frequency' => [
                'mode' => $request->input('freq', 'every_page'),
                'unit' => $request->input('freq_unit', 'days'),
                'value' => $request->input('freq_value', 1)
            ],
            'conditions' => [
                'stop' => $request->input('stop', 'never'),
                'views' => $request->input('views', 1)
            ]
        ];

        // Check if a settings row already exists for this announcement
        $announcementSettings = AnnouncementSetting::whereJsonContains('settings->announcement_id', (string) $announcementId)->first();

        if ($announcementSettings) {
            // Update existing
            $announcementSettings->settings = $settingsData;
            $announcementSettings->save();
        } else {
            // Create new
            AnnouncementSetting::create([
                'settings' => $settingsData
            ]);
        }

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }



    public function emailsettingupdate(Request $request)
    {
        $request->validate([
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255|unique:announcement_email_settings,from_email',
            'location_id' => 'nullable|string|max:255',
            'priviet_key' => 'nullable|string|max:255',
        ]);
        // $validated['user_id'] = auth()->id();

        AnnouncementEmailSetting::Create(

            [
                'user_id' => auth()->id(),
                'from_name' => $request->from_name,
                'from_email' => $request->from_email,
                'location_id' => $request->location_id,
                'priviet_key' => $request->priviet_key,
            ]
        );

        return back()->with('success', 'Email settings updated successfully.');
    }

}




