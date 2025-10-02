<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Models\CustomValue;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomValueController extends Controller
{
    public function index()
    {
        $values = CustomValue::where('user_id',login_id())->paginate(10);
        $userPermissions = UserPermission::where('user_id', auth()->id())
            ->get()
            ->groupBy('module')
            ->map(function ($rows) {
                return $rows->pluck('permission')->unique()->toArray();
            })
            ->toArray();
        return view('admin.custom_values.index', compact('values','userPermissions'));
    }

    public function create()
    {
        return view('admin.custom_values.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'value' => 'required|string|max:191',
        ]);
        // add current user_id automatically
        $data = $request->only('name', 'value');
        $data['user_id'] = auth()->id();

        CustomValue::create($data);

        return redirect()
            ->route('admin.customvalue.index')
            ->with('success', 'Custom Value saved!');
    }
    public function edit($id)
    {
        $value = CustomValue::findOrFail($id);
        return view('admin.custom_values.edit', compact('value'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'value' => 'required|string|max:191',
        ]);

        $value = CustomValue::findOrFail($id);
        // update including user_id
        $value->update(array_merge($request->only('name', 'value'), ['user_id' => auth()->id()]));

        return redirect()
            ->route('admin.customvalue.index')
            ->with('success', 'Custom Value updated');
    }

    public function destroy($id)
    {  
        $value = CustomValue::findOrFail($id);
        $value->delete();

        return redirect()->route('admin.customvalue.index')->with('success', 'Custom Value deleted');
    }

    public function apiIndex(Request $request)
    {
        $manualKey = $request->query('security_key');
        $superAdminEmail = $request->query('superadminemail');

        // 1. Master key
        $masterKey = Setting::where('key', 'crm_master_key')->value('value');
        if (!$masterKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Master key not configured'
            ], 500);
        }

        // 2. Get user
        $user = User::where('email', $superAdminEmail)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // 3. Validate final key
        $generatedFinalKey = $masterKey . $manualKey;
        if (!$manualKey || !Hash::check($generatedFinalKey, $user->final_key)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid manual key'
            ], 403);
        }

        // 4. Fetch only this user's custom values
        $query = CustomValue::where('user_id', $user->id);
        if ($request->has('name')) {
            $query->where('name', $request->name);
        }
        return response()->json($query->latest()->get());
    }
}