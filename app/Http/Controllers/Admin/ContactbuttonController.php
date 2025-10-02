<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CRM;
use App\Http\Controllers\Controller;
use App\Models\GhlAuth;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Models\Contacts;
use Illuminate\Support\Facades\Hash;

class ContactbuttonController extends Controller
{
    public function index()
    {
        $contacts = Contacts::where('user_id',login_id())->paginate(10);

        $userPermissions = UserPermission::where('user_id', auth()->id())
            ->get()
            ->groupBy('module')
            ->map(function ($rows) {
                return $rows->pluck('permission')->unique()->toArray();
            })
            ->toArray();

        return view('admin.contactsbutton.index', compact('contacts', 'userPermissions'));
    }

    public function store(Request $request)
    {

        dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'action' => 'nullable|in:url,tag',
            'url' => 'nullable|url|required_if:action,url|max:500',
            'tag' => 'nullable|string|max:100',
            'iframe' => 'nullable|boolean',
            'classes' => 'nullable|string|max:255',
            'locations' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'background' => 'nullable|string|max:20',
        ]);
        $validated['user_id'] = auth()->id();

        Contacts::create($validated);

        return back()->with('success', 'Contact added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'action' => 'nullable|in:url,tag',
            'url' => 'nullable|url|required_if:action,url|max:500',
            'iframe' => 'nullable|boolean',
            'classes' => 'nullable|string|max:255',
            'locations' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'background' => 'nullable|string|max:20',
        ]);

        $contact = Contacts::findOrFail($id);
        $validated['user_id'] = auth()->id();
        $contact->update($validated);

        return back()->with('success', 'Contact updated successfully!');
    }

    public function destroy($id)
    {
        $contact = Contacts::findOrFail($id);
        if ($contact->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this link.');
        }
        $contact->delete();

        return back()->with('success', 'Contact deleted successfully!');
    }

    public function apiIndex(Request $request)
    {
        $manualKey = $request->query('security_key');
        $superAdminEmail = $request->query('superadminemail');

        // -------------------------------------------------
        // 1. Master key fetch karo
        // -------------------------------------------------
        $masterKey = Setting::where('key', 'crm_master_key')->value('value');
        if (!$masterKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Master key not configured'
            ], 500);
        }

        // -------------------------------------------------
        // 2. User ka final key fetch karo
        // -------------------------------------------------
        $user = User::where('email', $superAdminEmail)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userFinalKey = $user->final_key;
        $generatedFinalKey = $masterKey . $manualKey;

        // -------------------------------------------------
        // 3. Validate final key
        // -------------------------------------------------
        if (!$manualKey || !Hash::check($generatedFinalKey, $userFinalKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid manual key'
            ], 403);
        }

        // -------------------------------------------------
        // 4. Contacts fetch (sirf current user ke)
        // -------------------------------------------------
        $contacts = Contacts::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json($contacts);
    }

}
