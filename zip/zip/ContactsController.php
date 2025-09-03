<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Contacts;

class ContactsController extends Controller
{
    public function index()
    {
        $contacts = Contacts::latest()->paginate(10);
        return view('admin.contacts.index', compact('contacts'));
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
