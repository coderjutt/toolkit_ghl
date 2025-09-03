<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RenameMenu;
use App\Models\User;
use Illuminate\Http\Request;

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
    public function renameGetApi($locationId)
    {
        // Step 1: Find user by location_id
        $user = User::where('location_id', $locationId)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found for this location ID',
            ], 404);
        }

        // Step 2: Fetch RenameMenu entries for that user
        $menus = RenameMenu::where('user_id', $user->id)->get(['old_menu', 'renamed_menu', 'image_url']);

        if ($menus->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No renamed menus found',
                'data'    => [],
            ], 200);
        }

        // Step 3: Format output
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
