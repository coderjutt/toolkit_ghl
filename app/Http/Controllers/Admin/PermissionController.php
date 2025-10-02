<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserScriptPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', '!=', 1)->get(); // exclude super admin
        $selectedUserId = $request->get('user');
        //   $selectedUserId = 2;

        // Load module/permission config
        // $modules = config('permissions.modules');
        // $modules = Modules::all();
        $scriptedModules = Modules::where('type', 'scripted')->get();
        $nonScriptedModules = Modules::where('type', 'non-scripted')->get();
        // Load saved permissions
        $savedPermissions = $selectedUserId
            ? UserPermission::where('user_id', $selectedUserId)->pluck('permission')->toArray()
            : [];

        $savedScriptPermissions = $selectedUserId
            ? UserScriptPermission::where('user_id', $selectedUserId)->pluck('permission')->toArray()
            : [];
        // dd($savedPermissions,$savedScriptPermissions);

        return view('admin.permissions.index', compact(
            'users',
            'selectedUserId',
            'nonScriptedModules',
            'scriptedModules',
            'savedPermissions',
            'savedScriptPermissions'
        ));

    }


    public function fetch($userId)
    {

        $permissions = UserPermission::where('user_id', $userId)->get(['module', 'permission']);
        $scriptPermissions = UserScriptPermission::where('user_id', $userId)->pluck('permission')->toArray();

        $moduleMap = [];
        foreach ($permissions as $item) {
            $moduleMap[$item->module][] = $item->permission;
        }

        return response()->json([
            'permissions' => $moduleMap,
            'script_permissions' => $scriptPermissions,
        ]);
    }

    public function update(Request $request)
    {
        $userId = $request->input('user_id');

        // Clear previous permissions
        UserPermission::where('user_id', $userId)->delete();
        UserScriptPermission::where('user_id', $userId)->delete();

        // Store main module permissions
        $permissions = $request->input('permissions', []);
        foreach ($permissions as $perm) {
            if (strpos($perm, '.') !== false) {
                [$module, $right] = explode('.', $perm, 2);
                UserPermission::create([
                    'user_id' => $userId,
                    'module' => $module,
                    'permission' => $right,
                ]);
            }
        }

        // Store script permissions
        $scriptPermissions = $request->input('script_permissions', []);
        foreach ($scriptPermissions as $perm) {
            UserScriptPermission::create([
                'user_id' => $userId,
                'permission' => $perm,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Permissions saved successfully.']);
    }

    public function storeModules(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'type' => 'required|in:scripted,non-scripted', // only allow these two values
        ]);

        // Create module
        Modules::create([
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [], // store empty array if null
            'type' => $validated['type'],
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Module created successfully!');
    }

    public function fetchapi(Request $request)
    {

        $email = $request->query('superadminemail');
        $manualKey = $request->query('security_key');

        // Step 1: Check manual key
        if (!$manualKey) {
            return response()->json([
                'success' => false,
                'message' => 'Manual key is required'
            ], 403);
        }

        // Step 2: User find
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Step 3: Get master key from settings
        $masterKey = Setting::where('key', 'crm_master_key')->value('value');
        if (!$masterKey) {
            return response()->json(['success' => false, 'message' => 'Master key not configured'], 500);
        }

        // Step 4: Validate final key
        $generatedFinalKey = $masterKey . $manualKey;
        if (!Hash::check($generatedFinalKey, $user->final_key)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid manual key'
            ], 403);
        }
        $scriptPermissions = UserScriptPermission::where('user_id', $user->id)
            ->get(['permission']);
        return response()->json([
            'script_permissions' => $scriptPermissions,
        ]);
    }

}
