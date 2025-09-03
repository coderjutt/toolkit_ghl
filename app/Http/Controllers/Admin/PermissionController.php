<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserScriptPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $users          = User::where('role', '!=', 1)->get(); // exclude super admin
        $selectedUserId = $request->get('user');

        // Load module/permission config
        // $modules = config('permissions.modules');
        $modules=Modules::all();
        // Load saved permissions
        $savedPermissions = $selectedUserId
        ? UserPermission::where('user_id', $selectedUserId)->pluck('permission')->toArray()
        : [];
      
        $savedScriptPermissions = $selectedUserId
        ? UserScriptPermission::where('user_id', $selectedUserId)->pluck('permission')->toArray()
        : [];
          
        return view('admin.permissions.index', compact(
            'users',
            'selectedUserId',
            'modules',
            'savedPermissions',
            'savedScriptPermissions'
        ));
 
    }


    public function fetch($userId)
    {
       
        $permissions       = UserPermission::where('user_id', $userId)->get(['module', 'permission']);
        $scriptPermissions = UserScriptPermission::where('user_id', $userId)->pluck('permission')->toArray();

        $moduleMap = [];
        foreach ($permissions as $item) {
            $moduleMap[$item->module][] = $item->permission;
        }

        return response()->json([
            'permissions'        => $moduleMap,
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
                    'user_id'    => $userId,
                    'module'     => $module,
                    'permission' => $right,
                ]);
            }
        }

        // Store script permissions
        $scriptPermissions = $request->input('script_permissions', []);
        foreach ($scriptPermissions as $perm) {
            UserScriptPermission::create([
                'user_id'    => $userId,
                'permission' => $perm,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Permissions saved successfully.']);
    }

    public function storeModules(Request $request){
           // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);

        // Create module
        Modules::create([
            'name' => $request->name,
            'permissions' => $request->permissions, // array of permissions
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Module created successfully!');
    
    }

}
