<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Models\Contacts;

class ContactsController extends Controller
{
    public function index()
    {
        $contacts = Contacts::latest()->paginate(10);

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
        $validated = $request->validate([
            'title'      => 'required|string|max:191',
            'action'     => 'nullable|in:url,tag',
            'url'        => 'nullable|url|required_if:action,url|max:500',
            'iframe'     => 'nullable|boolean',
            'classes'    => 'nullable|string|max:255',
            'locations'  => 'nullable|string|max:255',
            'folder'     => 'nullable|string|max:255',
            'color'      => 'nullable|string|max:20',
            'background' => 'nullable|string|max:20',
        ]);

        Contacts::create($validated);

        return back()->with('success', 'Contact added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:191',
            'action'     => 'nullable|in:url,tag',
            'url'        => 'nullable|url|required_if:action,url|max:500',
            'iframe'     => 'nullable|boolean',
            'classes'    => 'nullable|string|max:255',
            'locations'  => 'nullable|string|max:255',
            'folder'     => 'nullable|string|max:255',
            'color'      => 'nullable|string|max:20',
            'background' => 'nullable|string|max:20',
        ]);

        $contact = Contacts::findOrFail($id);
        $contact->update($validated);

        return back()->with('success', 'Contact updated successfully!');
    }

    public function destroy($id)
    {
        $contact = Contacts::findOrFail($id);
        $contact->delete();

        return back()->with('success', 'Contact deleted successfully!');
    }

    public function apiIndex()
    {
        $contacts = Contacts::latest()->paginate(20); // safer than all()
        return response()->json($contacts);
    }
}
