<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RenameMenu;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RenamemenuController extends Controller
{
    public function index()
    {
        $rawMenus = [
            "Launchpad", "Dashboard", "Conversations", "Calendars", "Contacts", "Opportunities", "Payments", "Marketing", "Automation", "Sites", "Reputation", "Reporting", "Private Community",
            [
                'label'       => 'Conversations',
                'childLabels' => ["Manul Actions", "Templates", "Trigger Links", "Trigger Analyze"],
            ],
            [
                'label'       => 'Calendars',
                'childLabels' => ["Appointments", "Smart Lists", "Manage Smart List", "Tasks"],
            ],
            [
                'label'       => 'Opportunities',
                'childLabels' => ["Pipelines"],
            ],
            [
                'label'       => 'Payments',
                'childLabels' => ["Create Product", "Products", "Interations", "Invoices", "Invoices Settings", "Recurring Templates", "Tax Settings", "Orders", "Subscriptions", "Transactions", "Coupons", "Create Coupons"],
            ],
            [
                'label'       => 'Marketing',
                'childLabels' => ["Create Coupons", "Email Templates", "Email Campaigns", "Templates", "Triggers Links", "Triggers Analyze", "Workflows", "Create Workflows", "Campaigns", "Triggers"],
            ],
            [
                'label'       => 'Sites',
                'childLabels' => ["Funnels", "Websites", "Blogs", "Forms Builder", "Forms Analyze", "Forms Submissions", "Survey Builder", "Survey Analyze", "Survey Submissions", "Chat Widget"],
            ],
            [
                'label'       => 'Memberships',
                'childLabels' => ["Dashboard", "Products", "Analytics", "Revenue Analytics", "Settings", "Site Details", "Custom Domain", "Email Settings"],
            ],
            [
                'label'       => 'Reputation',
                'childLabels' => ["Overview", "Requests", "Reviews", "Listings"],
            ],
            [
                'label'       => 'Reporting',
                'childLabels' => ["Google Ads", "Appointments Reports", "Call Reporting"],
            ],
            [
                'label'       => 'Settings',
                'childLabels' => ["Business Profile", "Conversation Providers", "Reputation Management", "Intefrations", "Facebook Form Fields Mapping", "TikTok Form Fields Mapping", "Pipelines", "Phone Numbers", "My Staff", "Custom Fields", "Tags", "Custom Values", "Calendars", "Preferences", "Availability", "Connections", "Email Services", "Media", "Domains", "URL Redirect"],
            ],
        ];

        // Flattened menu list (Parent >> Child)
        $menus = [];

        foreach ($rawMenus as $item) {
            if (is_string($item)) {
                $menus[] = $item;
            } elseif (is_array($item) && isset($item['label']) && isset($item['childLabels'])) {
                foreach ($item['childLabels'] as $child) {
                    $menus[] = $item['label'] . ' >> ' . $child;
                }
            }
        }

        // Fetch existing renamed menus for this user
        $savedMenus = RenameMenu::where('user_id', login_id())
            ->get()
            ->keyBy('old_menu'); // So we can access like $savedMenus['Dashboard']->renamed_menu

        return view('admin.modules.renameMenu.index', compact('menus', 'savedMenus'));
    }

    public function store(Request $request)
    {
        $menus  = $request->input('menus', []);
        $userId = login_id();

        // Delete existing user menus first
        RenameMenu::where('user_id', $userId)->delete();

        // Save new entries
        foreach ($menus as $menuKey => $fields) {
            if (is_array($fields) && (! empty($fields['label']) || ! empty($fields['image']))) {
                RenameMenu::create([
                    'old_menu'     => $menuKey,
                    'renamed_menu' => $fields['label'] ?? null,
                    'image_url'    => $fields['image'] ?? null,
                    'user_id'      => $userId,
                ]);
            }

        }

        return response()->json([
            'success' => true,
            'message' => 'Menu labels saved successfully',
        ]);
    }
   public function renameGetApi(Request $request)
{
    $manualKey = $request->query('security_key');
    $superAdminEmail = $request->query('superadminemail');

    if (!$manualKey || !$superAdminEmail) {
        return response()->json([
            'success' => false,
            'message' => 'Manual key and superadmin email are required',
        ], 400);
    }

    // Step 1: Master key
    $masterKey = Setting::where('key', 'crm_master_key')->value('value');
    if (!$masterKey) {
        return response()->json([
            'success' => false,
            'message' => 'Master key not configured',
        ], 500);
    }

    // Step 2: Get user by superadmin email
    $user = User::where('email', $superAdminEmail)->first();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    // Step 3: Validate final key
    $generatedFinalKey = $masterKey . $manualKey;
    if (!Hash::check($generatedFinalKey, $user->final_key)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid manual key',
        ], 403);
    }

    // Step 4: Fetch RenameMenu entries for this user + their user_id
    $menus = RenameMenu::where('user_id', $user->id)
        ->orWhere('user_id', $request->query('user_id')) // optional: user_id ke behalf
        ->get(['old_menu', 'renamed_menu', 'image_url']);

    if ($menus->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'No renamed menus found',
            'data'    => [],
        ], 200);
    }

    // Step 5: Format output
    $result = [];
    foreach ($menus as $menu) {
        $result[$menu->old_menu] = [
            'label' => $menu->renamed_menu,
            'image' => $menu->image_url,
        ];
    }

    return response()->json([
        'success' => true,
        'data'    => $result,
    ], 200);
    }

}
