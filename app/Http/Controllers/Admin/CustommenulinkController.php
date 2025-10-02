<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CustomMenuLink;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPermission;
use Hash;
use Illuminate\Http\Request;
use PharIo\Manifest\Url;

class CustommenulinkController extends Controller
{
    public function index()
    {
        $CMLink = CustomMenuLink::where('user_id', login_id())->get();
        $userPermissions = UserPermission::where('user_id', auth()->id())
            ->get()
            ->groupBy('module')
            ->map(function ($rows) {
                return $rows->pluck('permission')->unique()->toArray();
            })
            ->toArray();
        // dd($CMLink);
        return view('admin.custommenulink.index', compact('CMLink', 'userPermissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Title' => 'required|string|max:255',
            'Url' => 'nullable|string',
            'restricted_email' => 'nullable|array',
            'action' => 'nullable|string',
            'useTitleDropdown' => 'nullable|boolean',
        ]);

        // Agar dropdown use ho raha hai, to action null kar do
        if ($request->boolean('useTitleDropdown')) {
            $data['action'] = null;
            $data['Url'] = null; // Optional: Url bhi null karna ho to
        }

        $data['user_id'] = auth()->id();

        $link = CustomMenuLink::create($data);

        return response()->json([
            'success' => true,
            'data' => $link
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'Title' => 'required|string|max:255',
            'Url' => 'nullable|string',
            'restricted_email' => 'nullable|array',
            'action' => 'nullable|string',
            'useTitleDropdownEdit' => 'nullable|boolean',
        ]);
//   dd($data);
        // Agar dropdown use ho raha hai, to action null kar do
        if ($request->boolean('useTitleDropdownEdit')) {
            $data['action'] = null;
            $data['Url'] = null; // Optional: Url bhi null karna ho to
        }

        $data['user_id'] = auth()->id();

        $link = CustomMenuLink::findOrFail($id);
        $link->update($data);

        return response()->json([
            'success' => true,
            'data' => $link
        ]);
    }



    public function destroy($id)
    {
        $link = CustomMenuLink::findOrFail($id);

        // ✅ Check if current user is the owner
        if ($link->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this link.');
        }

        $link->delete();
        return redirect()->back()->with('success', 'Delete Successfully');
    }
    public function getapi_Custommenulink(Request $request)
    {
        $email = $request->query('superadminemail');
        $manualKey = $request->query('security_key');
        if (!$manualKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Manual key is required'
            ], 403);
        }

        // Step 1: Find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }
        // Step 2: Get master key from settings
        $masterKey = Setting::where('key', 'crm_master_key')->value('value');
        if (!$masterKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Master key not configured'
            ], 500);
        }

        // Step 3: Generate raw key string
        $generatedFinalKey = $masterKey . $manualKey;

        // Step 4: Validate against hashed final_key
        if (!Hash::check($generatedFinalKey, $user->final_key)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid manual key'
            ], 403);
        }

        // ✅ Step 5: Fetch only the links created by this user
        $customMenuLinks = CustomMenuLink::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $customMenuLinks
        ]);
    }



}
