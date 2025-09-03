<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementEmailSetting;
use App\Models\AnnouncementSetting;
use App\Models\AnnouncementView;
use App\Models\GlobaViewAnnouncements;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Log;
use Str;

class AnnouncementController extends Controller
{
    // public function getAnnouncements(Request $request)
    // {
    //     // Optional CORS headers
    //     header("Access-Control-Allow-Origin: *");
    //     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //     header("Access-Control-Allow-Headers: Content-Type, Authorization");

    //     $userEmail = $request->query('email') ?? null;
    //     $superAdminEmail = $request->query('superadminemail') ?? null;
    //     $now = Carbon::now();

    //     // ✅ Pehle check karo ke email announcement ke user_id se match karti hai ya nahi
    //     $matched = Announcement::whereNotNull('user_id')
    //         ->get()
    //         ->contains(function ($announcement) use ($userEmail, $superAdminEmail) {
    //             $user = User::find($announcement->user_id);
    //             if (!$user)
    //                 return false;

    //             $dbEmail = strtolower($user->email);

    //             if (!empty($superAdminEmail) && strtolower($superAdminEmail) === $dbEmail) {
    //                 return true;
    //             }
    //             if (!empty($userEmail) && strtolower($userEmail) === $dbEmail) {
    //                 return true;
    //             }
    //             return false;
    //         });

    //     if (!$matched) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Email not authorized for announcements'
    //         ], 403);
    //     }

    //     // Get active announcements
    //     $announcements = Announcement::where('status', 'active')
    //         ->where(function ($query) use ($now) {
    //             $query->where('expiry_type', 'never')
    //                 ->orWhere(function ($q) use ($now) {
    //                     $q->where('expiry_type', 'date')
    //                         ->where('expiry_date', '>=', $now);
    //                 });
    //         })
    //         ->get()
    //         ->filter(function ($announcement) use ($userEmail, $superAdminEmail) {

    //             if ($announcement->audience_type === 'all') {
    //                 return true;
    //             }

    //             // 🔹 Specific audience check
    //             if ($announcement->audience_type === 'specific') {
    //                 $locationsRaw = $announcement->locations;

    //                 // Handle different formats (json string, array, object)
    //                 if (is_string($locationsRaw)) {
    //                     $locations = json_decode($locationsRaw, true);
    //                 } elseif (is_array($locationsRaw)) {
    //                     $locations = $locationsRaw;
    //                 } elseif (is_object($locationsRaw)) {
    //                     $locations = (array) $locationsRaw;
    //                 } else {
    //                     $locations = [];
    //                 }

    //                 // 🔹 Separate arrays
    //                 $allowed_by_emails = [];
    //                 $allowed_location_ids = [];

    //                 foreach ($locations as $loc) {
    //                     if (!empty($loc['email'])) {
    //                         $emails = array_map('trim', explode(',', $loc['email']));
    //                         $allowed_by_emails = array_merge($allowed_by_emails, $emails);
    //                     }

    //                     if (!empty($loc['location_id'])) {
    //                         $allowed_location_ids[] = $loc['location_id'];
    //                     }
    //                 }

    //                 Log::info("📂 Parsed locations", [
    //                     "announcement_id" => $announcement->id,
    //                     "allowed_emails" => $allowed_by_emails,
    //                     "allowed_location_ids" => $allowed_location_ids,
    //                 ]);

    //                 // ✅ User email check
    //                 if (!empty($userEmail) && in_array(strtolower($userEmail), array_map('strtolower', $allowed_by_emails))) {
    //                     Log::info("✅ User email matched", [
    //                         "announcement_id" => $announcement->id,
    //                         "userEmail" => $userEmail
    //                     ]);
    //                     return true;
    //                 }

    //                 // ✅ Super Admin email check
    //                 if (!empty($superAdminEmail) && in_array(strtolower($superAdminEmail), array_map('strtolower', $allowed_by_emails))) {
    //                     Log::info("✅ Super Admin email matched", [
    //                         "announcement_id" => $announcement->id,
    //                         "superAdminEmail" => $superAdminEmail
    //                     ]);
    //                     return true;
    //                 }

    //                 Log::info("⏭ Skipped (no email match)", [
    //                     "announcement_id" => $announcement->id,
    //                     "userEmail" => $userEmail,
    //                     "superAdminEmail" => $superAdminEmail
    //                 ]);
    //                 return false;
    //             }

    //             return false;
    //         })
    //         ->map(function ($announcement) {
    //             // 🔹 Normalize locations
    //             $locationsRaw = $announcement->locations;

    //             if (is_string($locationsRaw)) {
    //                 $locations = json_decode($locationsRaw, true);
    //             } elseif (is_array($locationsRaw)) {
    //                 $locations = $locationsRaw;
    //             } elseif (is_object($locationsRaw)) {
    //                 $locations = (array) $locationsRaw;
    //             } else {
    //                 $locations = [];
    //             }

    //             // 🔹 Collect allowed emails & location IDs
    //             $allowed_by_emails = [];
    //             $allowed_location_ids = [];

    //             foreach ($locations as $loc) {
    //                 if (!empty($loc['email'])) {
    //                     $emails = array_map('trim', explode(',', $loc['email']));
    //                     $allowed_by_emails = array_merge($allowed_by_emails, $emails);
    //                 }
    //                 if (!empty($loc['location_id'])) {
    //                     $allowed_location_ids[] = $loc['location_id'];
    //                 }
    //             }

    //             // Add in response
    //             $announcement->allowed_by_email = $allowed_by_emails;
    //             $announcement->locations_id = $allowed_location_ids;

    //             return $announcement;
    //         })
    //         ->filter(function ($announcement) use ($userEmail) {
    //             // check user ne is announcement ko pehle dekha ya nahi
    //             $view = AnnouncementView::where('announcement_id', $announcement->id)
    //                 ->where('email', $userEmail)
    //                 ->first();

    //             // agar setting "never_again" hai to ek bar ke baad na dikhaye
    //             if ($announcement->display_setting === 'never_again') {
    //                 $skip = (bool) $view; // agar pehle se ek bhi record hai to skip
    //                 \Log::info("📌 never_again check", [
    //                     'announcement_id' => $announcement->id,
    //                     'viewExists' => $skip,
    //                     'userEmail' => $userEmail,
    //                 ]);
    //                 return !$view; // agar record hai to false (skip), warna true (show)
    //             }

    //             if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
    //                 preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
    //                 $allowed = $matches[1] ?? 1;
    //                 $views = $view ? $view->views : 0;

    //                 return $views < $allowed;
    //             }

    //             // agar koi special setting nahi hai to always show
    //             return true;
    //         })
    //         ->values(); // reset keys

    //     // Email sending logic
    //     foreach ($announcements as $announcement) {
    //         if ($announcement->allow_email && !$announcement->send_email) {
    //             $mailSettingsList = AnnouncementEmailSetting::all();

    //             if ($mailSettingsList->isEmpty()) {
    //                 Log::warning("⚠ No mail settings found for announcement {$announcement->id}");
    //                 continue;
    //             }

    //             try {
    //                 foreach ($mailSettingsList as $mailSettings) {
    //                     if (!$mailSettings->from_email)
    //                         continue;

    //                     // Log::info("📧 Sending email for announcement {$announcement->id} to {$mailSettings->from_email}");

    //                     Mail::to($mailSettings->from_email)
    //                         ->send(new \App\Mail\AnnouncementMail($announcement));
    //                 }

    //                 $announcement->update(['send_email' => 1]);
    //                 Log::info("✅ Email marked as sent for announcement {$announcement->id}");
    //             } catch (\Exception $e) {
    //                 Log::error("❌ Announcement email error: " . $e->getMessage());
    //             }
    //         }
    //     }

    //     return response()->json($announcements);
    // }



    // public function getAnnouncements(Request $request)
    // {
    //     // Optional CORS headers
    //     header("Access-Control-Allow-Origin: *");
    //     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //     header("Access-Control-Allow-Headers: Content-Type, Authorization");

    //     $userEmail = $request->query('email') ?? null;
    //     $superAdminEmail = $request->query('superadminemail') ?? null;
    //     $requestedAudienceType = $request->query('audience_type') ?? null;

    //     $now = Carbon::now();

    //     // ✅ Pehle check karo ke email announcement ke user_id se match karti hai ya nahi
    //     $matched = Announcement::whereNotNull('user_id')
    //         ->get()
    //         ->contains(function ($announcement) use ($userEmail, $superAdminEmail) {
    //             $user = User::find($announcement->user_id);
    //             if (!$user)
    //                 return false;

    //             $dbEmail = strtolower($user->email);

    //             if (!empty($superAdminEmail) && strtolower($superAdminEmail) === $dbEmail) {
    //                 return true;
    //             }
    //             if (!empty($userEmail) && strtolower($userEmail) === $dbEmail) {
    //                 return true;
    //             }
    //             return false;
    //         });

    //     if (!$matched) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Email not authorized for announcements'
    //         ], 403);
    //     }

    //     // Get active announcements
    //     $announcements = Announcement::where('status', 'active')
    //         ->where(function ($query) use ($now) {
    //             $query->where('expiry_type', 'never')
    //                 ->orWhere(function ($q) use ($now) {
    //                     $q->where('expiry_type', 'date')
    //                         ->where('expiry_date', '>=', $now);
    //                 });
    //         })
    //         ->get()
    //         ->filter(function ($announcement) use ($userEmail, $superAdminEmail) {

    //             if ($announcement->audience_type === 'all') {
    //                 return true;
    //             }

    //             // 🔹 Specific audience check
    //             if ($announcement->audience_type === 'specific') {
    //                 $locationsRaw = $announcement->locations;

    //                 if (is_string($locationsRaw)) {
    //                     $locations = json_decode($locationsRaw, true);
    //                 } elseif (is_array($locationsRaw)) {
    //                     $locations = $locationsRaw;
    //                 } elseif (is_object($locationsRaw)) {
    //                     $locations = (array) $locationsRaw;
    //                 } else {
    //                     $locations = [];
    //                 }

    //                 $allowed_by_emails = [];
    //                 $allowed_location_ids = [];

    //                 foreach ($locations as $loc) {
    //                     if (!empty($loc['email'])) {
    //                         $emails = array_map('trim', explode(',', $loc['email']));
    //                         $allowed_by_emails = array_merge($allowed_by_emails, $emails);
    //                     }

    //                     if (!empty($loc['location_id'])) {
    //                         $allowed_location_ids[] = $loc['location_id'];
    //                     }
    //                 }

    //                 if (!empty($userEmail) && in_array(strtolower($userEmail), array_map('strtolower', $allowed_by_emails))) {
    //                     return true;
    //                 }

    //                 if (!empty($superAdminEmail) && in_array(strtolower($superAdminEmail), array_map('strtolower', $allowed_by_emails))) {
    //                     return true;
    //                 }

    //                 return false;
    //             }

    //             return false;
    //         })
    //         ->map(function ($announcement) {
    //             $locationsRaw = $announcement->locations;

    //             if (is_string($locationsRaw)) {
    //                 $locations = json_decode($locationsRaw, true);
    //             } elseif (is_array($locationsRaw)) {
    //                 $locations = $locationsRaw;
    //             } elseif (is_object($locationsRaw)) {
    //                 $locations = (array) $locationsRaw;
    //             } else {
    //                 $locations = [];
    //             }

    //             $allowed_by_emails = [];
    //             $allowed_location_ids = [];

    //             foreach ($locations as $loc) {
    //                 if (!empty($loc['email'])) {
    //                     $emails = array_map('trim', explode(',', $loc['email']));
    //                     $allowed_by_emails = array_merge($allowed_by_emails, $emails);
    //                 }
    //                 if (!empty($loc['location_id'])) {
    //                     $allowed_location_ids[] = $loc['location_id'];
    //                 }
    //             }

    //             $announcement->allowed_by_email = $allowed_by_emails;
    //             $announcement->locations_id = $allowed_location_ids;

    //             return $announcement;
    //         })
    //         ->filter(function ($announcement) use ($userEmail) {
    //             $view = AnnouncementView::where('announcement_id', $announcement->id)
    //                 ->where('email', $userEmail)
    //                 ->first();

    //             if ($announcement->display_setting === 'never_again') {
    //                 return !$view;
    //             }

    //             if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
    //                 preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
    //                 $allowed = $matches[1] ?? 1;
    //                 $views = $view ? $view->views : 0;

    //                 return $views < $allowed;
    //             }

    //             return true;
    //         })
    //         ->values();

    //     // 🔹 Global settings filter (announcement_settings table)
    //     $announcements = $announcements->filter(function ($announcement) use ($userEmail, $requestedAudienceType) {
    //         $setting = AnnouncementSetting::first(); // Global settings
    //         $settings = $setting ? $setting->settings : []; // Already array, no json_decode
    //         // ✅ Audience role check from announcement_settings
    //         // dd($setting);
    //         if (!empty($settings['audience']['types'])) {
    //             $user = User::where('email', $userEmail)->first();
    //             if ($user && !in_array($user->role, $settings['audience']['types'])) {
    //                 return false;
    //             }
    //         }

    //         // 🔹 Audience type from request must match settings audience_type
    //         // if ($requestedAudienceType && !empty($settings['audience']['audience_type'])) {
    //         //     if ($requestedAudienceType !== $settings['audience']['audience_type']) {
    //         //         return false;
    //         //     }
    //         // }

    //         if ($requestedAudienceType) {
    //             $allowedTypes = $settings['audience']['types'] ?? [];
    //             if (!in_array($requestedAudienceType, $allowedTypes)) {
    //                 return false;
    //             }
    //         }

    //         // ✅ Frequency filter
    //         if (!empty($settings['frequency'])) {
    //             $freq = $settings['frequency'];
    //             $mode = $freq['mode'] ?? null;
    //             $unit = $freq['unit'] ?? null;
    //             $value = (int) ($freq['value'] ?? 0);

    //             $view = AnnouncementView::where('announcement_id', $announcement->id)
    //                 ->where('email', $userEmail)
    //                 ->latest()
    //                 ->first();

    //             if ($view && $mode !== 'every_page' && $unit && $value > 0) {
    //                 $nextAllowed = Carbon::parse($view->updated_at)->add($unit, $value);
    //                 if (Carbon::now()->lessThan($nextAllowed))
    //                     return false;
    //             }
    //         }

    //         // ✅ Stop after X views
    //         if (!empty($settings['conditions']['stop']) && $settings['conditions']['stop'] === 'after_views') {
    //             $allowedViews = (int) ($settings['conditions']['views'] ?? 1);
    //             $view = AnnouncementView::where('announcement_id', $announcement->id)
    //                 ->where('email', $userEmail)
    //                 ->first();
    //             $userViews = $view ? $view->views : 0;
    //             if ($userViews >= $allowedViews)
    //                 return false;
    //         }

    //         return true;
    //     })->values();

    //     // Email sending logic
    //     foreach ($announcements as $announcement) {
    //         if ($announcement->allow_email && !$announcement->send_email) {
    //             $mailSettingsList = AnnouncementEmailSetting::all();

    //             if ($mailSettingsList->isEmpty()) {
    //                 continue;
    //             }

    //             try {
    //                 foreach ($mailSettingsList as $mailSettings) {
    //                     if (!$mailSettings->from_email)
    //                         continue;

    //                     Mail::to($mailSettings->from_email)
    //                         ->send(new \App\Mail\AnnouncementMail($announcement));
    //                 }

    //                 $announcement->update(['send_email' => 1]);
    //             } catch (\Exception $e) {
    //                 Log::error("❌ Announcement email error: " . $e->getMessage());
    //             }
    //         }
    //     }

    //     return response()->json($announcements);
    // }

    public function getAnnouncements(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $userEmail = $request->query('email') ?? null;
        $superAdminEmail = $request->query('superadminemail') ?? null;
        $requestedAudienceType = $request->query('audience_type') ?? null;
        // $announcementId = $data['announcement_id'];
        $now = Carbon::now();

        // Authorization check
        $matched = Announcement::whereNotNull('user_id')
            ->get()
            ->contains(function ($announcement) use ($userEmail, $superAdminEmail) {
                $user = User::find($announcement->user_id);
                if (!$user)
                    return false;

                $dbEmail = strtolower($user->email);
                return (!empty($superAdminEmail) && strtolower($superAdminEmail) === $dbEmail)
                    || (!empty($userEmail) && strtolower($userEmail) === $dbEmail);
            });

        if (!$matched) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not authorized for announcements'
            ], 403);
        }

        // Fetch active announcements
        $announcements = Announcement::where('status', 'active')
            ->where(function ($query) use ($now) {
                $query->where('expiry_type', 'never')
                    ->orWhere(function ($q) use ($now) {
                        $q->where('expiry_type', 'date')->where('expiry_date', '>=', $now);
                    });
            })
            ->get();

        // Map emails and locations
        $announcements->transform(function ($announcement) {
            $locationsRaw = $announcement->locations;
            if (is_string($locationsRaw))
                $locations = json_decode($locationsRaw, true);
            elseif (is_array($locationsRaw))
                $locations = $locationsRaw;
            elseif (is_object($locationsRaw))
                $locations = (array) $locationsRaw;
            else
                $locations = [];

            $allowed_by_emails = [];
            $allowed_location_ids = [];
            foreach ($locations as $loc) {
                if (!empty($loc['email'])) {
                    $emails = array_map('trim', explode(',', $loc['email']));
                    $allowed_by_emails = array_merge($allowed_by_emails, $emails);
                }
                if (!empty($loc['location_id']))
                    $allowed_location_ids[] = $loc['location_id'];
            }

            $announcement->allowed_by_email = $allowed_by_emails;
            $announcement->locations_id = $allowed_location_ids;

            return $announcement;
        });

        // Filter announcements based on audience_type (all / specific)
        $announcements = $announcements->filter(function ($announcement) use ($userEmail, $superAdminEmail) {
            if ($announcement->audience_type === 'all')
                return true;

            if ($announcement->audience_type === 'specific') {
                $emails = array_map('strtolower', $announcement->allowed_by_email);

                return (!empty($userEmail) && in_array(strtolower($userEmail), $emails))
                    || (!empty($superAdminEmail) && in_array(strtolower($superAdminEmail), $emails));
            }

            return false;
        });

        // -----------------------------
        // Separate filters for global and per-announcement
        // -----------------------------

        $globalSettings = AnnouncementSetting::first();
        $globalSettingsArray = $globalSettings->settings ?? [];

        // $announcementview=AnnouncementView::all();
        // dd($announcementview);
        $filtered = $announcements->filter(function ($announcement) use ($userEmail, $requestedAudienceType, $globalSettingsArray) {

            $include = true;
            $settings = $announcement->settings ?? [];

            // 🔹 Apply global settings ONLY if general_settings = true
            if (!empty($settings['general_settings'])) {

                // Audience types from global settings
                $allowedTypes = $globalSettingsArray['audience']['types'] ?? [];
                if ($requestedAudienceType && !in_array($requestedAudienceType, $allowedTypes)) {
                    $include = false;
                }

                // Frequency from global settings
                $globalSettingsArray = $globalSettingsArray ?? []; // make sure it's defined
                $freq = $globalSettingsArray['frequency'] ?? [];
                $conditions = $globalSettingsArray['conditions'] ?? [];

                // Get the latest view for this user and announcement
                $view = GlobaViewAnnouncements::where('announcement_id', $announcement->id)
                    ->where('user_email', $userEmail)
                    ->latest()
                    ->first();

                // --- Frequency check ---
                if (
                    $view && ($freq['mode'] ?? 'every_page') !== 'every_page'
                    && !empty($freq['unit']) && !empty($freq['value'])
                ) {
                    $nextAllowed = Carbon::parse($view->updated_at)->add($freq['unit'], (int) $freq['value']);
                    if (Carbon::now()->lessThan($nextAllowed)) {
                        $include = false;
                    }
                }

                // --- Stop conditions check ---
                $stop = $conditions['stop'] ?? null;
                $allowedViews = (int) ($conditions['views'] ?? 1);
                $userViews = $view ? $view->views : 0;

                if (($stop === 'after_views' || $stop === 'never_show_again') && $userViews >= $allowedViews) {
                    $include = false;

                }
            }

            // 🔹 Apply per-announcement filter ALWAYS, independent of global
            // Audience types per announcement
            // Per-announcement audience filter
            $allowedTypes = $settings['audience_types'] ?? [];
            // dd($allowedTypes);
            if ($requestedAudienceType && !empty($allowedTypes)) {
                if (!in_array($requestedAudienceType, $allowedTypes)) {
                    $include = false;
                }
                // return false;
            }
            // Frequency per announcement
            $freq = $settings['frequency'] ?? [];
            $mode = $freq['mode'] ?? ($freq['type'] ?? null);
            $unit = $freq['unit'] ?? null;
            $value = (int) ($freq['value'] ?? 0);
            //     dd(
//     $announcement->id,
//     $userEmail,
//     AnnouncementView::where('announcement_id', $announcement->id)->pluck('email')
// );
            $view = AnnouncementView::where('announcement_id', $announcement->id)
                ->where('email', $userEmail)
                ->latest()
                ->first();
            //  dd($view);

            if ($view && $mode !== 'every_page' && $unit && $value > 0) {
                $nextAllowed = Carbon::parse($view->updated_at)->add($unit, $value);
                if (Carbon::now()->lessThan($nextAllowed))
                    $include = false;
            }

            // Stop conditions per announcement
            if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
                preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
                $allowed = $matches[1] ?? 1;
                $views = $view ? $view->views : 0;

                return $views < $allowed;
            }


            return $include;
        })->values();



        // Email sending logic
        foreach ($filtered as $announcement) {
            if ($announcement->allow_email && !$announcement->send_email) {
                $mailSettingsList = AnnouncementEmailSetting::all();
                foreach ($mailSettingsList as $mailSettings) {
                    if (!$mailSettings->from_email)
                        continue;
                    try {
                        Mail::to($mailSettings->from_email)
                            ->send(new \App\Mail\AnnouncementMail($announcement));
                    } catch (\Exception $e) {
                        Log::error("❌ Announcement email error: " . $e->getMessage());
                    }
                }
                Announcement::query()->update(['send_email' => 1]);
            }
        }

        return response()->json($filtered);
    }




    // public function markAsViewed(Request $request)
    // {
    //     try {
    //         $data = $request->validate([
    //             'announcement_id' => 'required|integer',
    //             'ghl_user_id' => 'nullable|string',
    //             'location_id' => 'nullable|string',
    //             'email' => 'required|email',
    //             'user_id' => 'nullable|string',
    //             'type' => 'required|string',
    //             'role' => 'required|string',
    //         ]);

    //         $email = trim($data['email']);

    //         // 👇 Combine type + role
    //         $audienceType = strtolower($data['type'] . '_' . $data['role']);
    //         // dd($audienceType);
    //         // 🔹 Existing record find ya create karo
    //         $view = AnnouncementView::firstOrNew([
    //             'announcement_id' => $data['announcement_id'],
    //             'email' => $email,
    //         ]);

    //         $view->ghl_user_id = $data['ghl_user_id'] ?? $view->ghl_user_id;
    //         $view->location_id = $data['location_id'] ?? $view->location_id;
    //         $view->user_id = $data['user_id'] ?? $view->user_id;
    //         // dd(vars: $view);
    //         // 👇 Audience type update (comma separated string)
    //         $existingTypes = $view->audience_type ? explode(',', $view->audience_type) : [];

    //         if (!in_array($audienceType, $existingTypes)) {
    //             $existingTypes[] = $audienceType;
    //         }

    //         $view->audience_type = implode(',', $existingTypes);
    //         //   dd($view);

    //         $view->views = ($view->views ?? 0) + 1;
    //         $view->save();

    //         Log::info("👁 Announcement marked as viewed", [
    //             'announcement_id' => $data['announcement_id'],
    //             'email' => $email,
    //             'audience_type' => $view->audience_type,
    //             'totalViews' => $view->views,
    //         ]);

    //         return response()->json([
    //             'message' => 'View recorded successfully',
    //             'views' => $view->views,
    //             'saved' => $view,
    //         ], 200);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'message' => 'Validation failed',
    //             'error' => $e->errors(),
    //             'views' => null,
    //             'saved' => null,
    //         ], 422);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage(),
    //             'views' => null,
    //             'saved' => null,
    //         ], 500);
    //     }
    // }


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
//     try {
//         $data = $request->validate([
//             'user_email'      => 'required|string|email',
//             'user_id'         => 'nullable|integer',
//             'location_id'     => 'nullable|integer',
//             'ghl_user_id'     => 'nullable|integer',
//             'announcement_id' => 'required|integer|exists:announcements,id', // validate request
//         ]);


    //         // Fetch the announcement setting if needed
//         $setting = AnnouncementSetting::all(); // or filter by announcement_id if needed

    //         foreach ($setting as $s) {
//             $settings   = $s->settings;
//             $frequency  = $settings['frequency']  ?? null;
//             $conditions = $settings['conditions'] ?? null;

    //             // Save or update the global view for this user and announcement
//             GlobaViewAnnouncements::updateOrCreate(
//                 [
//                     'user_email'     => $data['user_email'],
//                     'announcement_id'=> $data['announcement_id'], // from request
//                 ],
//                 [
//                     'frequency'   => $frequency,
//                     'conditions'  => $conditions,
//                     'location_id' => $data['location_id'] ?? null,
//                     'user_id'     => $data['user_id'] ?? null,
//                     'ghl_user_id' => $data['ghl_user_id'] ?? null,
//                 ]
//             );
//         }

    //         return response()->json([
//             'status'  => 'success',
//             'message' => 'Global announcement view saved for user',
//         ], 201);

    //     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => 'error',
//             'message' => $e->getMessage(),
//         ], 500);
//     }
// }



    public function storeGlobalViewAnnouncements(Request $request)
    {
        try {
            $data = $request->validate([
                'announcement_id' => 'required|numeric', // works with bigints
                'ghl_user_id' => 'nullable|string',
                'location_id' => 'nullable|string',
                'user_email' => 'required|email',
                'user_id' => 'nullable|numeric', // match bigint
               
            ]);

            // Fetch announcements where general_settings is true
            $announcements = Announcement::all(['id', 'settings'])
                ->filter(fn($row) => !empty($row->settings['general_settings']) && $row->settings['general_settings'] === true);

            // Fetch all global announcement settings
            $globalSettings = AnnouncementSetting::all();

            foreach ($globalSettings as $setting) {
                $settings = $setting->settings;
                $frequency = $settings['frequency'] ?? null;
                $conditions = $settings['conditions'] ?? null;

                foreach ($announcements as $announcement) {
                    // Save or update for each user_email and announcement
                    GlobaViewAnnouncements::updateOrCreate(
                        [
                            'user_email' => $data['user_email'], // unique per user
                            'announcement_id' => $announcement->id,   // link to announcement
                        ],
                        [
                            'frequency' => $frequency,
                            'conditions' => $conditions,
                            'location_id' => $data['location_id'] ?? null,
                            'user_id' => $data['user_id'] ?? null,
                            'ghl_user_id' => $data['ghl_user_id'] ?? null,
                        ]
                    );
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Global announcement view saved for user',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}