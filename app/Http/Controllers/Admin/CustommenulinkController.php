<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CustomMenuLink;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class CustommenulinkController extends Controller
{
    public function index()
    {
        $CMLink = CustomMenuLink::all();
        $userPermissions = UserPermission::where('user_id', auth()->id())
            ->get()
            ->groupBy('module')
            ->map(function ($rows) {
                return $rows->pluck('permission')->unique()->toArray();
            })
            ->toArray();
        return view('admin.custommenulink.index', compact('CMLink', 'userPermissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Title' => 'required|string|max:255',
            'allowed_emails' => 'nullable|array',
            'restricted_email' => 'nullable|array',
            'action' => 'required|string'
        ]);

        $link = CustomMenuLink::create($data);

        return response()->json(['success' => true, 'data' => $link]);
    }
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'Title' => 'required|string|max:255',
            'allowed_emails' => 'nullable|array',
            'restricted_email' => 'nullable|array',
            'action' => 'required|string'
        ]);

        $link = CustomMenuLink::findOrFail($id);
        $link->update($data);

        return response()->json(['success' => true, 'data' => $link]);
    }
    public function destroy($id)
    {
        $link = CustomMenuLink::findOrFail($id);
        $link->delete();
        return redirect()->back()->with('success', 'Delete Successfully');
    }
    public function getapi_Custommenulink($email)
    {
        // Step 1: Check user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not authorized for announcements',
            ], 403);
        }

        // Step 2: Fetch all CustomMenuLinks
        $customMenuLinks = CustomMenuLink::all();

        return response()->json([
            'success' => true,
            'data' => $customMenuLinks
        ]);
    }



}
