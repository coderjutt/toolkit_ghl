<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementEmailSetting;
use App\Models\AnnouncementSetting;
use App\Models\AnnouncementView;
use App\Models\GlobaViewAnnouncements;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
// use Log;
use Str;

class AnnouncementController extends Controller
{

    // public function getAnnouncements(Request $request)
    // {
    //     header("Access-Control-Allow-Origin: *");
    //     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //     header("Access-Control-Allow-Headers: Content-Type, Authorization");

    //     $userEmail = $request->query('email') ?? null;
    //     $superAdminEmail = $request->query('superadminemail') ?? null;
    //     $requestedAudienceType = $request->query('audience_type') ?? null;
    //     $manualKey = $request->query('security_key') ?? null;
    //     $now = Carbon::now();

    //     // -----------------------------------------------------
    //     // 1. Final Key Matching (Super Admin)
    //     // -----------------------------------------------------

    //     $masterKey = Setting::where('key', 'crm_master_key')->value('value');
    //     if (!$masterKey) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Master key not configured'
    //         ], 500);
    //     }

    //     // Super admin ke email se final key fetch karo
    //     $userFinalKey = User::where('email', $superAdminEmail)->value('final_key');

    //     // Generate expected final key
    //     $generatedFinalKey = $masterKey . $manualKey;

    //     // Verify final key
    //     if (!$manualKey || !Hash::check($generatedFinalKey, $userFinalKey)) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Invalid manual key'
    //         ], 403);
    //     }

    //     // -----------------------------------------------------
    //     // 2. Fetch Active Announcements
    //     // -----------------------------------------------------
    //     $announcements = Announcement::where('status', 'active')
    //         ->where(function ($query) use ($now) {
    //             $query->where('expiry_type', 'never')
    //                 ->orWhere(function ($q) use ($now) {
    //                     $q->where('expiry_type', 'date')->where('expiry_date', '>=', $now);
    //                 });
    //         })
    //         ->get();

    //     // -----------------------------------------------------
    //     // 3. Map allowed emails & locations
    //     // -----------------------------------------------------
    //     $announcements->transform(function ($announcement) {
    //         $locationsRaw = $announcement->locations;
    //         $locations = is_string($locationsRaw) ? json_decode($locationsRaw, true)
    //             : (is_array($locationsRaw) ? $locationsRaw : (is_object($locationsRaw) ? (array) $locationsRaw : []));
    //         //    dd($locations);
    //         $allowedEmails = [];
    //         $allowed_location_ids = [];

    //         foreach ($locations as $loc) {
    //             if (!empty($loc['email'])) {
    //                 $emails = array_map(fn($e) => strtolower(trim($e)), explode(',', $loc['email']));
    //                 $allowedEmails = array_merge($allowedEmails, $emails);
    //             }

    //             if (!empty($loc['location_id'])) {
    //                 $allowed_location_ids[] = trim($loc['location_id']);
    //             }
    //         }

    //         // Remove duplicates
    //         $allowedEmails = array_unique($allowedEmails);
    //         $allowed_location_ids = array_unique($allowed_location_ids);

    //         // Assign to announcement
    //         $announcement->allowed_by_email = $allowedEmails;
    //         $announcement->allowed_location_ids = $allowed_location_ids;
    //         // dd($announcement->allowed_by_email,$allowed_location_ids);
    //         return $announcement;
    //     });

    //     // -----------------------------------------------------
    //     // 4. Filter by Audience Type (announcement.audience_type)
    //     // -----------------------------------------------------
    //     // $announcements = $announcements->filter(function ($announcement) use ($userEmail) {
    //     //     if ($announcement->audience_type === 'all') {
    //     //         return true; // show all
    //     //     }
    //     //     if ($announcement->audience_type === 'specific') {
    //     //         $emails = array_map('strtolower', $announcement->allowed_by_email);
    //     //         return !empty($userEmail) && in_array(strtolower($userEmail), $emails);
    //     //     }
    //     //     return false;
    //     // });

    //     $announcements = $announcements->filter(function ($announcement) use ($userEmail, $superAdminEmail) {

    //         $announcementUser = User::find($announcement->user_id);

    //         if (!$announcementUser) {
    //             return false;
    //         }

    //         $dbEmail = strtolower($announcementUser->email);
    //         $userEmail = strtolower($userEmail ?? '');
    //         $superAdminEmail = strtolower($superAdminEmail ?? '');

    //         // ✅ Agar superadmin ka email aur user_id dono match karte hain → hamesha allow
    //         if (!empty($superAdminEmail) && $superAdminEmail === $dbEmail) {
    //             return true;
    //         }

    //         // Audience = all -> sirf creator ko show karo
    //         if ($announcement->audience_type === 'all') {
    //             return !empty($userEmail) && $userEmail === $dbEmail;
    //         }

    //         // Audience = specific -> allowed emails check
    //         if ($announcement->audience_type === 'specific') {
    //             $emails = array_map('strtolower', $announcement->allowed_by_email ?? []);
    //             return !empty($userEmail) && in_array($userEmail, $emails);
    //         }

    //         return false;
    //     });


    //     // -----------------------------------------------------
    //     // 5. Global Settings Load
    //     // -----------------------------------------------------
    //     $globeluser = User::where('email', $superAdminEmail)->first();
    //     $globalSettings = AnnouncementSetting::where('user_id', $globeluser->id)->first();
    //     // dd($globalSettings);
    //     $globalSettingsArray = $globalSettings
    //         ? (is_string($globalSettings->settings)
    //             ? json_decode($globalSettings->settings, true)
    //             : ($globalSettings->settings ?? []))
    //         : [];

    //     // -----------------------------------------------------
    //     // 6. Final Filter (audienceType + general_settings)
    //     // -----------------------------------------------------
    //     $filtered = $announcements->filter(function ($announcement) use ($userEmail, $requestedAudienceType, $globalSettingsArray) {
    //         $settings = is_string($announcement->settings)
    //             ? json_decode($announcement->settings, true)
    //             : ($announcement->settings ?? []);

    //         if (!empty($settings['general_settings']) && $settings['general_settings'] === true) {
    //             $allowedTypes = $globalSettingsArray['audience']['types'] ?? [];
    //             if (!$requestedAudienceType)
    //                 return false;
    //             if ($requestedAudienceType && !in_array($requestedAudienceType, $allowedTypes))
    //                 return false;
    //             $freq = $globalSettingsArray['frequency'] ?? [];
    //             $conditions = $globalSettingsArray['conditions'] ?? [];
    //             // $viesa=GlobaViewAnnouncements::all();
    //             // dd($viesa);
    //             $view = GlobaViewAnnouncements::firstOrCreate(
    //                 [
    //                     'announcement_id' => $announcement->id,
    //                     'user_email' => $userEmail,
    //                 ],
    //                 [
    //                     'frequency' => [],
    //                     'conditions' => [
    //                         'current_views' => 0,
    //                         'never_show' => false,
    //                         'never_stop' => false
    //                     ],
    //                 ]
    //             );
    //             // dd($view);
    //             $userConditions = $view->conditions ?? [];
    //             $userViews = $userConditions['current_views'] ?? 0;
    //             $frequencyLogs = $view->frequency ?? [];

    //             $frequencyRule = $freq['type'] ?? 'every_page';
    //             $gap = $freq['gap'] ?? null;

    //             if (!$this->canShowAnnouncement($frequencyRule, $frequencyLogs, $gap))
    //                 return false;

    //             if (($userConditions['never_stop'] ?? false) === true)
    //                 return false;
    //             if (($conditions['stop'] ?? null) === 'never_show_again' && ($userConditions['never_show'] ?? false))
    //                 return false;
    //             if (($conditions['stop'] ?? null) === 'after_views' && $userViews >= ($conditions['views'] ?? 1))
    //                 return false;

    //             return true;
    //         }

    //         $allowedTypes = $settings['audience_types'] ?? [];
    //         if (!$requestedAudienceType)
    //             return false;
    //         if ($requestedAudienceType && !empty($allowedTypes) && !in_array($requestedAudienceType, $allowedTypes))
    //             return false;

    //         $freq = $settings['frequency'] ?? [];
    //         $mode = $freq['mode'] ?? ($freq['type'] ?? null);
    //         $unit = $freq['unit'] ?? null;
    //         $value = (int) ($freq['value'] ?? 0);

    //         $view = AnnouncementView::where('announcement_id', $announcement->id)
    //             ->where('email', $userEmail)
    //             ->latest()
    //             ->first();

    //         // dd($view, $announcement->id,$view->view, $announcement->title);
    //         if ($view && $mode !== 'every_page' && $unit && $value > 0) {
    //             $nextAllowed = Carbon::parse($view->updated_at)->add($unit, $value);
    //             if (Carbon::now()->lessThan($nextAllowed))
    //                 return false;
    //         }

    //         if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
    //             preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
    //             $allowed = $matches[1] ?? 1;
    //             $views = $view ? $view->views : 0;
    //             if ($views >= $allowed)
    //                 return false;
    //         }
    //         // dd($announcement->id, $announcement->title, $views, $allowed);
    //         return true;
    //     })->values();

    //     //  -------------------------------
    //     // Email sending logic
    //     foreach ($filtered as $announcement) {
    //         if ($announcement->allow_email && !$announcement->send_email) {
    //             $mailSettingsList = AnnouncementEmailSetting::where('user_id', $announcement->user_id)->get();
    //             foreach ($mailSettingsList as $mailSettings) {
    //                 if (!$mailSettings->from_email)
    //                     continue;
    //                 try {
    //                     Mail::to($mailSettings->from_email)
    //                         ->send(new \App\Mail\AnnouncementMail($announcement));
    //                 } catch (\Exception $e) {
    //                     // Log::error("Announcement email error: " . $e->getMessage());
    //                 }
    //             }
    //             Announcement::where('user_id', $announcement->user_id)
    //                 ->update(['send_email' => 1]);
    //         }
    //     }

    //     return response()->json($filtered);
    // }

  
        public function getAnnouncements(Request $request)
        {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");

            $userEmail = strtolower($request->query('email') ?? '');
            $superAdminEmail = strtolower($request->query('superadminemail') ?? '');
            $requestedAudienceType = $request->query('audience_type') ?? null;
            $manualKey = $request->query('security_key') ?? null;
            $now = Carbon::now();

            // -----------------------------------------------------
            // 1. Final Key Matching (Super Admin)
            // -----------------------------------------------------
            $masterKey = Setting::where('key', 'crm_master_key')->value('value');
            if (!$masterKey) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Master key not configured'
                ], 500);
            }

            $userFinalKey = User::where('email', $superAdminEmail)->value('final_key');
            if (!$manualKey || !Hash::check($masterKey . $manualKey, $userFinalKey)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid secuirty key'
                ], 403);
            }

            // -----------------------------------------------------
            // 2. Restriction: Only Superadmin’s Own Announcements
            // -----------------------------------------------------
            $superAdminUser = User::where('email', $superAdminEmail)->first();

            if ($superAdminUser) {
                $announcements = Announcement::where('status', 'active')
                    ->where('user_id', $superAdminUser->id)
                    ->where(function ($query) use ($now) {
                        $query->where('expiry_type', 'never')
                            ->orWhere(function ($q) use ($now) {
                                $q->where('expiry_type', 'date')->where('expiry_date', '>=', $now);
                            });
                    })
                    ->get();
            } else {
                return response()->json([]);
            }

            // -----------------------------------------------------
            // 3. Map allowed emails & locations
            // -----------------------------------------------------
            $announcements->transform(function ($announcement) {
                $locationsRaw = $announcement->locations;
                $locations = is_string($locationsRaw) ? json_decode($locationsRaw, true)
                    : (is_array($locationsRaw) ? $locationsRaw : (is_object($locationsRaw) ? (array) $locationsRaw : []));

                $allowedEmails = [];
                $allowed_location_ids = [];

                foreach ($locations as $loc) {
                    if (!empty($loc['email'])) {
                        $emails = array_map(fn($e) => strtolower(trim($e)), explode(',', $loc['email']));
                        $allowedEmails = array_merge($allowedEmails, $emails);
                    }

                    if (!empty($loc['location_id'])) {
                        $allowed_location_ids[] = trim($loc['location_id']);
                    }
                }

                $announcement->allowed_by_email = array_unique($allowedEmails);
                $announcement->allowed_location_ids = array_unique($allowed_location_ids);

                return $announcement;
            });

            

            $announcements = $announcements->filter(function ($announcement) use ($userEmail, $superAdminEmail) {
                $announcementUser = User::find($announcement->user_id);
                if (!$announcementUser) {
                    return false;
                }

                $dbEmail = strtolower($announcementUser->email);

                // ✅ Superadmin ka apna email + id → hamesha allow (only for "all")
                if ($announcement->audience_type === 'all') {
                    return !empty($superAdminEmail) && $superAdminEmail === $dbEmail && $announcementUser->id === $announcement->user_id;
                }

                // ✅ Audience = specific → ab email required hai
                if ($announcement->audience_type === 'specific') {
                    if (empty($userEmail)) {
                        // agar userEmail request me hi nahi aaya → reject
                        return false;
                    }

                    $emails = array_map('strtolower', $announcement->allowed_by_email ?? []);
                    return in_array($userEmail, $emails);
                }

                return false;
            });




            // -----------------------------------------------------
            // 5. Global Settings Load
            // -----------------------------------------------------
            $globeluser = $superAdminUser;
            $globalSettings = AnnouncementSetting::where('user_id', $globeluser?->id)->first();
            $globalSettingsArray = $globalSettings
                ? (is_string($globalSettings->settings)
                    ? json_decode($globalSettings->settings, true)
                    : ($globalSettings->settings ?? []))
                : [];

            // -----------------------------------------------------
            // 6. Final Filter (audienceType + general_settings)
            // -----------------------------------------------------
            $filtered = $announcements->filter(function ($announcement) use ($userEmail, $requestedAudienceType, $globalSettingsArray) {
                $settings = is_string($announcement->settings)
                    ? json_decode($announcement->settings, true)
                    : ($announcement->settings ?? []);

              
                if (empty($requestedAudienceType)) {
                    return false;
                }

                // ----- Global settings mode -----
                if (!empty($settings['general_settings']) && $settings['general_settings'] === true) {
                    $allowedTypes = $globalSettingsArray['audience']['types'] ?? [];

                    
                    if (!in_array($requestedAudienceType, $allowedTypes)) {
                        return false;
                    }

                    $freq = $globalSettingsArray['frequency'] ?? [];
                    $conditions = $globalSettingsArray['conditions'] ?? [];

                    $view = GlobaViewAnnouncements::firstOrCreate(
                        [
                            'announcement_id' => $announcement->id,
                            'user_email' => $userEmail,
                        ],
                        [
                            'frequency' => [],
                            'conditions' => [
                                'current_views' => 0,
                                'never_show' => false,
                                'never_stop' => false
                            ],
                        ]
                    );

                    $userConditions = $view->conditions ?? [];
                    $userViews = $userConditions['current_views'] ?? 0;
                    $frequencyLogs = $view->frequency ?? [];

                    $frequencyRule = $freq['type'] ?? 'every_page';
                    $gap = $freq['gap'] ?? null;

                    if (!$this->canShowAnnouncement($frequencyRule, $frequencyLogs, $gap)) {
                        return false;
                    }

                    if (($userConditions['never_stop'] ?? false) === true) {
                        return false;
                    }

                    if (($conditions['stop'] ?? null) === 'never_show_again' && ($userConditions['never_show'] ?? false)) {
                        return false;
                    }

                    if (($conditions['stop'] ?? null) === 'after_views' && $userViews >= ($conditions['views'] ?? 1)) {
                        return false;
                    }

                    return true;
                }

                // ----- Per-announcement settings -----
                $allowedTypes = $settings['audience_types'] ?? [];

                //Agar announcement specific audience_types define karta hai aur request match nahi karti → reject
                if (!empty($allowedTypes) && !in_array($requestedAudienceType, $allowedTypes)) {
                    return false;
                }

                $freq = $settings['frequency'] ?? [];
                $mode = $freq['mode'] ?? ($freq['type'] ?? null);
                $unit = $freq['unit'] ?? null;
                $value = (int) ($freq['value'] ?? 0);

                $view = AnnouncementView::where('announcement_id', $announcement->id)
                    ->where('email', $userEmail)
                    ->latest()
                    ->first();

                if ($view && $mode !== 'every_page' && $unit && $value > 0) {
                    $nextAllowed = Carbon::parse($view->updated_at)->add($unit, $value);
                    if (Carbon::now()->lessThan($nextAllowed)) {
                        return false;
                    }
                }

                if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
                    preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
                    $allowed = $matches[1] ?? 1;
                    $views = $view ? $view->views : 0;
                    if ($views >= $allowed) {
                        return false;
                    }
                }

                return true;
            })->values();

            // -----------------------------------------------------
            // 7. Email sending logic
            // -----------------------------------------------------
             foreach ($filtered as $announcement) {
            if ($announcement->allow_email && !$announcement->send_email) {
                $mailSettingsList = AnnouncementEmailSetting::where('user_id', $announcement->user_id)->get();
                foreach ($mailSettingsList as $mailSettings) {
                    if (!$mailSettings->from_email)
                        continue;
                    try {
                        Mail::to($mailSettings->from_email)
                            ->send(new \App\Mail\AnnouncementMail($announcement));
                    } catch (\Exception $e) {
                        // Log::error("Announcement email error: " . $e->getMessage());
                    }
                }
                Announcement::where('user_id', $announcement->user_id)
                    ->update(['send_email' => 1]);
            }
        }

            return response()->json($filtered);
        }




    public function markAsViewed(Request $request)
    {
        try {
            $data = $request->validate([
                'announcement_id' => 'required|integer',
                'ghl_user_id' => 'nullable|string',
                'location_id' => 'nullable|string',
                'email' => 'required|email',
                'user_id' => 'nullable|string',
                'type' => 'required|string',
                'role' => 'required|string',
            ]);

            $email = trim($data['email']);

            // 👇 Audience type banaye
            $audienceType = strtolower($data['type'] . '_' . $data['role']);

            // 👇 Announcement fetch karo
            $announcement = Announcement::find($data['announcement_id']);

            // 👇 View find ya create karo
            $view = AnnouncementView::firstOrNew([
                'announcement_id' => $data['announcement_id'],
                'email' => $email,
            ]);

            $view->ghl_user_id = $data['ghl_user_id'] ?? $view->ghl_user_id;
            $view->location_id = $data['location_id'] ?? $view->location_id;
            $view->user_id = $data['user_id'] ?? $view->user_id;

            // 👇 Audience type update (comma separated string)
            $existingTypes = $view->audience_type ? explode(',', $view->audience_type) : [];
            if (!in_array($audienceType, $existingTypes)) {
                $existingTypes[] = $audienceType;
            }
            $view->audience_type = implode(',', $existingTypes);

            // 👁 Views + Frequency
            $view->views = ($view->views ?? 0) + 1;
            $view->frequency = $announcement->settings['frequency'] ?? null;

            $view->save();

            return response()->json([
                'message' => 'View recorded successfully',
                'views' => $view->views,
                'frequency' => $view->frequency,
                'saved' => $view,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // public function storeGlobalViewAnnouncements(Request $request)
    // {
    //     $data = $request->validate([
    //         'user_email' => 'required|string|email',
    //         'user_id' => 'nullable|integer',
    //         'location_id' => 'nullable|integer',
    //         'ghl_user_id' => 'nullable|integer',
    //         'announcement_id' => 'required|integer',
    //     ]);

    //     try {
    //         $announcementSetting = AnnouncementSetting::first();
    //         if (!$announcementSetting) {
    //             return response()->json(['message' => 'Announcement settings not found.'], 404);
    //         }

    //         $settings = is_string($announcementSetting->settings)
    //             ? json_decode($announcementSetting->settings, true)
    //             : $announcementSetting->settings;

    //         if (!is_array($settings)) {
    //             return response()->json(['message' => 'Invalid announcement settings.'], 500);
    //         }

    //         $conditions = $settings['conditions'] ?? [];
    //         $frequency = $settings['frequency'] ?? [];
    //         $stopCondition = $conditions['stop'] ?? 'never';
    //         $maxViews = isset($conditions['views']) ? (int) $conditions['views'] : 0;

    //         $globalView = GlobaViewAnnouncements::firstOrCreate(
    //             [
    //                 'user_email' => $data['user_email'],
    //                 'announcement_id' => $data['announcement_id'],
    //             ],
    //             [
    //                 'user_id' => $data['user_id'] ?? null,
    //                 'location_id' => $data['location_id'] ?? null,
    //                 'ghl_user_id' => $data['ghl_user_id'] ?? null,
    //                 'frequency' => [],
    //                 'conditions' => ['current_views' => 0, 'never_show' => false],
    //             ]
    //         );

    //         $userConditions = $globalView->conditions ?? [];
    //         $currentViews = $userConditions['current_views'] ?? 0;
    //         $neverShowFlag = $userConditions['never_stop'] ?? false;

    //         if ($stopCondition === 'never_show_again' && $neverShowFlag) {
    //             return response()->json(['message' => 'never_show_again', 'current_views' => $currentViews], 200);
    //         }

    //         if ($stopCondition === 'after_views' && $maxViews > 0 && $currentViews >= $maxViews) {
    //             $userConditions['never_stop'] = true;
    //             $globalView->conditions = $userConditions;
    //             $globalView->save();

    //             return response()->json(['message' => 'max views reached', 'current_views' => $currentViews], 200);
    //         }

    //         $freqLogs = $globalView->frequency ?? [];
    //         $frequencyRule = $frequency['type'] ?? 'every_page';
    //         $gap = $frequency['gap'] ?? null;

    //         if (!$this->canShowAnnouncement($frequencyRule, $freqLogs, $gap)) {
    //             return response()->json([
    //                 'message' => "Blocked by frequency rule ($frequencyRule)",
    //                 'last_view' => !empty($freqLogs) ? end($freqLogs) : null,
    //             ], 200);
    //         }

    //         $userConditions['current_views'] = $currentViews + 1;
    //         if ($stopCondition === 'never_show_again') {
    //             $userConditions['never_show'] = true;
    //         }
    //         $globalView->conditions = $userConditions;

    //         // ✅ Save new frequency timestamp as string
    //         $freqLogs[] = now()->toDateTimeString();
    //         $globalView->frequency = $freqLogs;
    //         $globalView->save();

    //         return response()->json([
    //             'message' => 'Announcement view recorded.',
    //             'current_views' => $userConditions['current_views']
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Failed to store announcement view.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }




    public function storeGlobalViewAnnouncements(Request $request)
    {
        $data = $request->validate([
            'user_email' => 'required|email',
            'user_id' => 'nullable|integer',
            'location_id' => 'nullable|string',   // changed to string
            'ghl_user_id' => 'nullable|string',  // changed to string
            'announcement_id' => 'required|integer',
        ]);

        try {
            $announcementSetting = AnnouncementSetting::first();
            if (!$announcementSetting) {
                return response()->json(['message' => 'Announcement settings not found.'], 404);
            }

            $settings = is_string($announcementSetting->settings)
                ? json_decode($announcementSetting->settings, true)
                : $announcementSetting->settings;

            if (!is_array($settings)) {
                return response()->json(['message' => 'Invalid announcement settings.'], 500);
            }

            $conditions = $settings['conditions'] ?? [];
            $frequency = $settings['frequency'] ?? [];
            $stopCondition = $conditions['stop'] ?? 'never';
            $maxViews = isset($conditions['views']) ? (int) $conditions['views'] : 0;

            $globalView = GlobaViewAnnouncements::firstOrCreate(
                [
                    'user_email' => $data['user_email'],
                    'announcement_id' => $data['announcement_id'],
                ],
                [
                    'user_id' => $data['user_id'] ?? null,
                    'location_id' => $data['location_id'] ?? null,
                    'ghl_user_id' => $data['ghl_user_id'] ?? null,
                    'frequency' => [],
                    'conditions' => ['current_views' => 0, 'never_show' => false],
                ]
            );

            $userConditions = $globalView->conditions ?? [];
            $currentViews = $userConditions['current_views'] ?? 0;
            $neverShowFlag = $userConditions['never_show'] ?? false; // fixed key name

            // stop conditions
            if ($stopCondition === 'never_show_again' && $neverShowFlag) {
                return response()->json(['message' => 'never_show_again', 'current_views' => $currentViews], 200);
            }

            if ($stopCondition === 'after_views' && $maxViews > 0 && $currentViews >= $maxViews) {
                $userConditions['never_show'] = true; // fixed key name
                $globalView->conditions = $userConditions;
                $globalView->save();

                return response()->json(['message' => 'max views reached', 'current_views' => $currentViews], 200);
            }

            // frequency check
            $freqLogs = $globalView->frequency ?? [];
            $frequencyRule = $frequency['type'] ?? 'every_page';
            $gap = $frequency['gap'] ?? null;

            if (!$this->canShowAnnouncement($frequencyRule, $freqLogs, $gap)) {
                return response()->json([
                    'message' => "Blocked by frequency rule ($frequencyRule)",
                    'last_view' => !empty($freqLogs) ? end($freqLogs) : null,
                ], 200);
            }

            // update views + logs
            $userConditions['current_views'] = $currentViews + 1;
            $globalView->conditions = $userConditions;

            $freqLogs[] = now()->toDateTimeString();
            $globalView->frequency = $freqLogs;

            $globalView->save();

            return response()->json([
                'message' => 'Announcement view recorded.',
                'current_views' => $userConditions['current_views']
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to store announcement view.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function canShowAnnouncement($frequencyRule, $frequencyLogs, $gap = null)
    {
        $lastView = null;

        if (!empty($frequencyLogs)) {
            try {
                $lastView = Carbon::parse(end($frequencyLogs));
            } catch (\Exception $e) {
                $lastView = null; // agar parse na ho to ignore
            }
        }

        switch ($frequencyRule) {
            case 'every_page':
                return true;

            case 'once_per_session':
                $sessionKey = "announcement_shown_" . request('announcement_id');
                if (session()->has($sessionKey)) {
                    return false;
                }
                session()->put($sessionKey, true);
                return true;

            case 'once_every_hours':
                $gap = $gap ?: 1; // default 1 hour
                return !$lastView || now()->diffInHours($lastView) >= $gap;

            case 'once_every_days':
                $gap = $gap ?: 1; // default 1 day
                return !$lastView || now()->diffInDays($lastView) >= $gap;

            default:
                return true;
        }
    }


}