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

            // dd($userPermissions);
        return view('admin.contacts.index', compact('contacts','userPermissions'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:191',
        ]);

        Contacts::create($request->all());
        return back()->with('success', 'Contact added successfully!');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:191',
        ]);

        $contact = Contacts::findOrFail($id);
        $contact->update($request->all());
        return back()->with('success', 'Contact updated successfully!');
    }

    public function destroy($id)
    {
        Contacts::findOrFail($id)->delete();
        return back()->with('success', 'Contact deleted successfully!');
    }

    public function apiIndex()
    {
        $contacts = Contacts::all();
        return response()->json($contacts);
    }
}
