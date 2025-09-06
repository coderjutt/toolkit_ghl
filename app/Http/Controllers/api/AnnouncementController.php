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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
// use Log;
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

    // public function getAnnouncements(Request $request)
    // {
    //     header("Access-Control-Allow-Origin: *");
    //     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //     header("Access-Control-Allow-Headers: Content-Type, Authorization");

    //     $userEmail = $request->query('email') ?? null;
    //     $superAdminEmail = $request->query('superadminemail') ?? null;
    //     $requestedAudienceType = $request->query('audience_type') ?? null;
    //     // $announcementId = $data['announcement_id'];
    //     $now = Carbon::now();

    //     // Authorization check
    //     $matched = Announcement::whereNotNull('user_id')
    //         ->get()
    //         ->contains(function ($announcement) use ($userEmail, $superAdminEmail) {
    //             $user = User::find($announcement->user_id);
    //             if (!$user)
    //                 return false;

    //             $dbEmail = strtolower($user->email);
    //             return (!empty($superAdminEmail) && strtolower($superAdminEmail) === $dbEmail)
    //                 || (!empty($userEmail) && strtolower($userEmail) === $dbEmail);
    //         });

    //     if (!$matched) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Email not authorized for announcements'
    //         ], 403);
    //     }

    //     // Fetch active announcements
    //     $announcements = Announcement::where('status', 'active')
    //         ->where(function ($query) use ($now) {
    //             $query->where('expiry_type', 'never')
    //                 ->orWhere(function ($q) use ($now) {
    //                     $q->where('expiry_type', 'date')->where('expiry_date', '>=', $now);
    //                 });
    //         })
    //         ->get();

    //     // Map emails and locations
    //     $announcements->transform(function ($announcement) {
    //         $locationsRaw = $announcement->locations;
    //         if (is_string($locationsRaw))
    //             $locations = json_decode($locationsRaw, true);
    //         elseif (is_array($locationsRaw))
    //             $locations = $locationsRaw;
    //         elseif (is_object($locationsRaw))
    //             $locations = (array) $locationsRaw;
    //         else
    //             $locations = [];

    //         $allowed_by_emails = [];
    //         $allowed_location_ids = [];
    //         foreach ($locations as $loc) {
    //             if (!empty($loc['email'])) {
    //                 $emails = array_map('trim', explode(',', $loc['email']));
    //                 $allowed_by_emails = array_merge($allowed_by_emails, $emails);
    //             }
    //             if (!empty($loc['location_id']))
    //                 $allowed_location_ids[] = $loc['location_id'];
    //         }

    //         $announcement->allowed_by_email = $allowed_by_emails;
    //         $announcement->locations_id = $allowed_location_ids;

    //         return $announcement;
    //     });

    //     // Filter announcements based on audience_type (all / specific)
    //     $announcements = $announcements->filter(function ($announcement) use ($userEmail, $superAdminEmail) {
    //         if ($announcement->audience_type === 'all')
    //             return true;

    //         if ($announcement->audience_type === 'specific') {
    //             $emails = array_map('strtolower', $announcement->allowed_by_email);

    //             return (!empty($userEmail) && in_array(strtolower($userEmail), $emails))
    //                 || (!empty($superAdminEmail) && in_array(strtolower($superAdminEmail), $emails));
    //         }

    //         return false;
    //     });

    //     // -----------------------------
    //     // Separate filters for global and per-announcement
    //     // -----------------------------

    //     $globalSettings = AnnouncementSetting::first();
    //     $globalSettingsArray = $globalSettings->settings ?? [];

    //     $filtered = $announcements->filter(function ($announcement) use ($userEmail, $requestedAudienceType, $globalSettingsArray) {

    //         $include = true;
    //         $settings = is_string($announcement->settings)
    //             ? json_decode($announcement->settings, true)
    //             : ($announcement->settings ?? []);

    //         // 🔹 Apply GLOBAL settings if enabled
    //         if (!empty($settings['general_settings'])) {

    //             // Audience type filter
    //             $allowedTypes = $globalSettingsArray['audience']['types'] ?? [];
    //             if ($requestedAudienceType && !in_array($requestedAudienceType, $allowedTypes)) {
    //                 return false;
    //             }

    //             // Frequency & conditions
    //             $freq = $globalSettingsArray['frequency'] ?? [];
    //             $conditions = $globalSettingsArray['conditions'] ?? [];

    //             // Fetch or create global view record
    //             $view = GlobaViewAnnouncements::firstOrCreate(
    //                 [
    //                     'announcement_id' => $announcement->id,
    //                     'user_email' => $userEmail,
    //                 ],
    //                 [
    //                     'frequency' => [],
    //                     'conditions' => ['current_views' => 0, 'never_show' => false, 'never_stop' => false],
    //                 ]
    //             );

    //             $userConditions = $view->conditions ?? [];
    //             $userViews = $userConditions['current_views'] ?? 0;
    //             $frequencyLogs = $view->frequency ?? [];

    //             // Frequency check
    //             $frequencyRule = $freq['type'] ?? 'every_page';
    //             $gap = $freq['gap'] ?? null;

    //             if (!$this->canShowAnnouncement($frequencyRule, $frequencyLogs, $gap)) {
    //                 return false;
    //             }

    //             // User ke record ke conditions nikaalo
    //             $userConditions = $view ? ($view->conditions ?? []) : [];
    //             $userViews = $userConditions['current_views'] ?? 0;
    //             $neverStop = $userConditions['never_stop'] ?? false;
    //             $neverShow = $userConditions['never_show'] ?? false;

    //             // Global / announcement settings
    //             $stop = $conditions['stop'] ?? null;
    //             $allowedViews = (int) ($conditions['views'] ?? 1);

    //             // --- Stop conditions check ---
    //             if ($neverStop === true) {
    //                 // agar once reached max views -> hamesha band
    //                 $include = false;
    //             }

    //             if ($stop === 'never_show_again' && $neverShow === true) {
    //                 $include = false;
    //             }

    //             if ($stop === 'after_views' && $userViews >= $allowedViews) {
    //                 $include = false;
    //             }
    //         }

    //         // 🔹 Apply PER ANNOUNCEMENT settings
    //         $allowedTypes = $settings['audience_types'] ?? [];
    //         if ($requestedAudienceType && !empty($allowedTypes) && !in_array($requestedAudienceType, $allowedTypes)) {
    //             return false;
    //         }

    //         $freq = $settings['frequency'] ?? [];
    //         $mode = $freq['mode'] ?? ($freq['type'] ?? null);
    //         $unit = $freq['unit'] ?? null;
    //         $value = (int) ($freq['value'] ?? 0);

    //         $view = AnnouncementView::where('announcement_id', $announcement->id)
    //             ->where('email', $userEmail)
    //             ->latest()
    //             ->first();

    //         if ($view && $mode !== 'every_page' && $unit && $value > 0) {
    //             $nextAllowed = Carbon::parse($view->updated_at)->add($unit, $value);
    //             if (Carbon::now()->lessThan($nextAllowed)) {
    //                 return false;
    //             }
    //         }

    //         // Stop condition per announcement
    //         if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
    //             preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
    //             $allowed = $matches[1] ?? 1;
    //             $views = $view ? $view->views : 0;

    //             if ($views >= $allowed) {
    //                 return false;
    //             }
    //         }

    //         return $include;
    //     })->values();



    //     // Email sending logic
    //     foreach ($filtered as $announcement) {
    //         if ($announcement->allow_email && !$announcement->send_email) {
    //             $mailSettingsList = AnnouncementEmailSetting::all();
    //             foreach ($mailSettingsList as $mailSettings) {
    //                 if (!$mailSettings->from_email)
    //                     continue;
    //                 try {
    //                     Mail::to($mailSettings->from_email)
    //                         ->send(new \App\Mail\AnnouncementMail($announcement));
    //                 } catch (\Exception $e) {
    //                     Log::error("❌ Announcement email error: " . $e->getMessage());
    //                 }
    //             }
    //             Announcement::query()->update(['send_email' => 1]);
    //         }
    //     }

    //     return response()->json($filtered);
    // }


    // public function getAnnouncements(Request $request)
    // {
    //     header("Access-Control-Allow-Origin: *");
    //     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //     header("Access-Control-Allow-Headers: Content-Type, Authorization");

    //     $userEmail = $request->query('email') ?? null;
    //     $superAdminEmail = $request->query('superadminemail') ?? null;
    //     $requestedAudienceType = $request->query('audience_type') ?? null;
    //     $now = Carbon::now();

    //     // -------------------------------
    //     // Authorization check
    //     // -------------------------------
    //     $matched = Announcement::whereNotNull('user_id')
    //         ->get()
    //         ->contains(function ($announcement) use ($userEmail, $superAdminEmail) {
    //             $user = User::find($announcement->user_id);
    //             if (!$user)
    //                 return false;

    //             $dbEmail = strtolower($user->email);
    //             return (!empty($superAdminEmail) && strtolower($superAdminEmail) === $dbEmail)
    //                 || (!empty($userEmail) && strtolower($userEmail) === $dbEmail);
    //         });

    //     if (!$matched) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Email not authorized for announcements'
    //         ], 403);
    //     }

    //     // -------------------------------
    //     // Fetch active announcements
    //     // -------------------------------
    //     $announcements = Announcement::where('status', 'active')
    //         ->where(function ($query) use ($now) {
    //             $query->where('expiry_type', 'never')
    //                 ->orWhere(function ($q) use ($now) {
    //                     $q->where('expiry_type', 'date')->where('expiry_date', '>=', $now);
    //                 });
    //         })
    //         ->get();

    //     // -------------------------------
    //     // Map allowed emails & locations
    //     // -------------------------------
    //     $announcements->transform(function ($announcement) {
    //         $locationsRaw = $announcement->locations;
    //         if (is_string($locationsRaw)) {
    //             $locations = json_decode($locationsRaw, true);
    //         } elseif (is_array($locationsRaw)) {
    //             $locations = $locationsRaw;
    //         } elseif (is_object($locationsRaw)) {
    //             $locations = (array) $locationsRaw;
    //         } else {
    //             $locations = [];
    //         }

    //         $allowed_by_emails = [];
    //         $allowed_location_ids = [];
    //         foreach ($locations as $loc) {
    //             if (!empty($loc['email'])) {
    //                 $emails = array_map('trim', explode(',', $loc['email']));
    //                 $allowed_by_emails = array_merge($allowed_by_emails, $emails);
    //             }
    //             if (!empty($loc['location_id']))
    //                 $allowed_location_ids[] = $loc['location_id'];
    //         }

    //         $announcement->allowed_by_email = $allowed_by_emails;
    //         $announcement->locations_id = $allowed_location_ids;

    //         return $announcement;
    //     });

    //     // -------------------------------
    //     // Filter by audience_type
    //     // -------------------------------
    //     $announcements = $announcements->filter(function ($announcement) use ($userEmail, $superAdminEmail) {
    //         if ($announcement->audience_type === 'all')
    //             return true;

    //         if ($announcement->audience_type === 'specific') {
    //             $emails = array_map('strtolower', $announcement->allowed_by_email);
    //             return (!empty($userEmail) && in_array(strtolower($userEmail), $emails))
    //                 || (!empty($superAdminEmail) && in_array(strtolower($superAdminEmail), $emails));
    //         }
    //         return false;
    //     });

    //     // -------------------------------
    //     // Global settings load
    //     // -------------------------------
    //     $globalSettings = AnnouncementSetting::first();
    //     $globalSettingsArray = $globalSettings
    //         ? (is_string($globalSettings->settings)
    //             ? json_decode($globalSettings->settings, true)
    //             : ($globalSettings->settings ?? []))
    //         : [];

    //     // -------------------------------
    //     // Final filter (global + per-announcement)
    //     // -------------------------------
    //     $filtered = $announcements->filter(function ($announcement) use ($userEmail, $requestedAudienceType, $globalSettingsArray) {

    //         $settings = is_string($announcement->settings)
    //             ? json_decode($announcement->settings, true)
    //             : ($announcement->settings ?? []);

    //         // ==================================================
    //         // ✅ Global settings
    //         // ==================================================
    //         if (!empty($settings['general_settings'])) {

    //             // Audience type filter
    //             $allowedTypes = $globalSettingsArray['audience']['types'] ?? [];
    //             if ($requestedAudienceType && !in_array($requestedAudienceType, $allowedTypes)) {
    //                 return false;
    //             }

    //             // Frequency & conditions
    //             $freq = $globalSettingsArray['frequency'] ?? [];
    //             $conditions = $globalSettingsArray['conditions'] ?? [];

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

    //             $userConditions = $view->conditions ?? [];
    //             $userViews = $userConditions['current_views'] ?? 0;
    //             $frequencyLogs = $view->frequency ?? [];

    //             // Frequency check
    //             $frequencyRule = $freq['type'] ?? 'every_page';
    //             $gap = $freq['gap'] ?? null;
    //             if (!$this->canShowAnnouncement($frequencyRule, $frequencyLogs, $gap)) {
    //                 return false;
    //             }

    //             // Stop conditions
    //             $neverStop = $userConditions['never_stop'] ?? false;
    //             $neverShow = $userConditions['never_show'] ?? false;
    //             $stop = $conditions['stop'] ?? null;
    //             $allowedViews = (int) ($conditions['views'] ?? 1);

    //             if ($neverStop === true)
    //                 return false;
    //             if ($stop === 'never_show_again' && $neverShow === true)
    //                 return false;
    //             if ($stop === 'after_views' && $userViews >= $allowedViews)
    //                 return false;

    //             // ✅ Only global apply, skip per-announcement
    //             return true;
    //         }

    //         // ==================================================
    //         // ✅ Per announcement settings
    //         // ==================================================
    //         $allowedTypes = $settings['audience_types'] ?? [];
    //         if ($requestedAudienceType && !empty($allowedTypes) && !in_array($requestedAudienceType, $allowedTypes)) {
    //             return false;
    //         }

    //         $freq = $settings['frequency'] ?? [];
    //         $mode = $freq['mode'] ?? ($freq['type'] ?? null);
    //         $unit = $freq['unit'] ?? null;
    //         $value = (int) ($freq['value'] ?? 0);

    //         $view = AnnouncementView::where('announcement_id', $announcement->id)
    //             ->where('email', $userEmail)
    //             ->latest()
    //             ->first();

    //         if ($view && $mode !== 'every_page' && $unit && $value > 0) {
    //             $nextAllowed = Carbon::parse($view->updated_at)->add($unit, $value);
    //             if (Carbon::now()->lessThan($nextAllowed)) {
    //                 return false;
    //             }
    //         }

    //         // Stop condition per announcement
    //         if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
    //             preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
    //             $allowed = $matches[1] ?? 1;
    //             $views = $view ? $view->views : 0;

    //             if ($views >= $allowed)
    //                 return false;
    //         }

    //         return true;
    //     })->values();

    //     // -------------------------------
    //     // Email sending logic
    //     // -------------------------------
    //     foreach ($filtered as $announcement) {
    //         if ($announcement->allow_email && !$announcement->send_email) {
    //             $mailSettingsList = AnnouncementEmailSetting::all();
    //             foreach ($mailSettingsList as $mailSettings) {
    //                 if (!$mailSettings->from_email)
    //                     continue;
    //                 try {
    //                     Mail::to($mailSettings->from_email)
    //                         ->send(new \App\Mail\AnnouncementMail($announcement));
    //                 } catch (\Exception $e) {
    //                     Log::error("❌ Announcement email error: " . $e->getMessage());
    //                 }
    //             }
    //             $announcement->update(['send_email' => 1]); // ✅ Only update this one
    //         }
    //     }

    //     return response()->json($filtered);
    // }

    public function getAnnouncements(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $userEmail = $request->query('email') ?? null;
        $superAdminEmail = $request->query('superadminemail') ?? null;
        $requestedAudienceType = $request->query('audience_type') ?? null;
        $now = Carbon::now();

        // -----------------------------------------------------
        // 1. Super Admin Email Check
        // -----------------------------------------------------
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

        // -----------------------------------------------------
        // 2. Fetch Active Announcements
        // -----------------------------------------------------
        $announcements = Announcement::where('status', 'active')
            ->where(function ($query) use ($now) {
                $query->where('expiry_type', 'never')
                    ->orWhere(function ($q) use ($now) {
                        $q->where('expiry_type', 'date')->where('expiry_date', '>=', $now);
                    });
            })
            ->get();

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

            // Remove duplicates
            $allowedEmails = array_unique($allowedEmails);
            $allowed_location_ids = array_unique($allowed_location_ids);

            // Assign to announcement
            $announcement->allowed_by_email = $allowedEmails;
            $announcement->allowed_location_ids = $allowed_location_ids;
            // dd($announcement->allowed_by_email,$announcement->locations);
            return $announcement;
        });

        // -----------------------------------------------------
        // 4. Filter by Audience Type (announcement.audience_type)
        // -----------------------------------------------------
        $announcements = $announcements->filter(function ($announcement) use ($userEmail) {
            if ($announcement->audience_type === 'all') {
                return true; // show all
            }
            if ($announcement->audience_type === 'specific') {
                $emails = array_map('strtolower', $announcement->allowed_by_email);
                return !empty($userEmail) && in_array(strtolower($userEmail), $emails);
            }
            return false;
        });

        // -----------------------------------------------------
        // 5. Global Settings Load
        // -----------------------------------------------------
        $globalSettings = AnnouncementSetting::first();
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

            // -------------------------
            // ✅ Case A: General Settings = true
            // -------------------------
            if (!empty($settings['general_settings']) && $settings['general_settings'] === true) {
                $allowedTypes = $globalSettingsArray['audience']['types'] ?? [];
                if(!$requestedAudienceType){
                    return false;
                }
                if ($requestedAudienceType && !in_array($requestedAudienceType, $allowedTypes)) {
                    return false;
                }
                // dd($allowedTypes, $requestedAudienceType);
                // Frequency & Stop conditions
                $freq = $globalSettingsArray['frequency'] ?? [];
                $conditions = $globalSettingsArray['conditions'] ?? [];
                //  dd($conditions,$freq);
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
                // dd($view);
                $userConditions = $view->conditions ?? [];
                // dd($userConditions);
                $userViews = $userConditions['current_views'] ?? 0;
                $frequencyLogs = $view->frequency ?? [];

                $frequencyRule = $freq['type'] ?? 'every_page';
                $gap = $freq['gap'] ?? null;

                if (!$this->canShowAnnouncement($frequencyRule, $frequencyLogs, $gap)) {
                    return false;
                }

                if (($userConditions['never_stop'] ?? false) === true)
                    return false;
                if (($conditions['stop'] ?? null) === 'never_show_again' && ($userConditions['never_show'] ?? false))
                    return false;
                if (($conditions['stop'] ?? null) === 'after_views' && $userViews >= ($conditions['views'] ?? 1))
                    return false;

                return true;
            }

            // -------------------------
            // ✅ Case B: General Settings = false (per-announcement)
            // -------------------------
            $allowedTypes = $settings['audience_types'] ?? [];
            if(!$requestedAudienceType){
                    return false;
                }
            if ($requestedAudienceType && !empty($allowedTypes) && !in_array($requestedAudienceType, $allowedTypes)) {
                return false;
            }

            // Frequency Check
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

            // Stop Condition
            if (Str::startsWith($announcement->display_setting, 'stop_after_')) {
                preg_match('/stop_after_(\d+)_view/', $announcement->display_setting, $matches);
                $allowed = $matches[1] ?? 1;
                $views = $view ? $view->views : 0;

                if ($views >= $allowed)
                    return false;
            }

            return true;
        })->values();

        //  -------------------------------
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


    public function storeGlobalViewAnnouncements(Request $request)
    {
        $data = $request->validate([
            'user_email' => 'required|string|email',
            'user_id' => 'nullable|integer',
            'location_id' => 'nullable|integer',
            'ghl_user_id' => 'nullable|integer',
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
            $neverShowFlag = $userConditions['never_stop'] ?? false;

            if ($stopCondition === 'never_show_again' && $neverShowFlag) {
                return response()->json(['message' => 'never_show_again', 'current_views' => $currentViews], 200);
            }

            if ($stopCondition === 'after_views' && $maxViews > 0 && $currentViews >= $maxViews) {
                $userConditions['never_stop'] = true;
                $globalView->conditions = $userConditions;
                $globalView->save();

                return response()->json(['message' => 'max views reached', 'current_views' => $currentViews], 200);
            }

            $freqLogs = $globalView->frequency ?? [];
            $frequencyRule = $frequency['type'] ?? 'every_page';
            $gap = $frequency['gap'] ?? null;

            if (!$this->canShowAnnouncement($frequencyRule, $freqLogs, $gap)) {
                return response()->json([
                    'message' => "Blocked by frequency rule ($frequencyRule)",
                    'last_view' => !empty($freqLogs) ? end($freqLogs) : null,
                ], 200);
            }

            $userConditions['current_views'] = $currentViews + 1;
            if ($stopCondition === 'never_show_again') {
                $userConditions['never_show'] = true;
            }
            $globalView->conditions = $userConditions;

            // ✅ Save new frequency timestamp as string
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