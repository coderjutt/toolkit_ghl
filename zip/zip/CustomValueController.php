<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CustomValue;

class CustomValueController extends Controller
{
    public function index()
    {
        $values = CustomValue::latest()->paginate(10);
        return view('admin.custom_values.index', compact('values'));
    }

    public function create()
    {
        return view('admin.custom_values.create');
    }
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'company_email' => 'required|email',
            'agent_name' => 'required|string|max:191',
        ]);
        CustomValue::create($request->only('company_email', 'agent_name'));

        return redirect()
            ->route('admin.api.custom-values.index')
            ->with('success', 'Custom Value saves!');
    }
    public function edit($id)
    {
        // dd($id); 
        $value = CustomValue::findOrFail($id);
        return view('admin.custom_values.edit', compact('value'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'company_email' => 'required|email',
            'agent_name' => 'required|string|max:191',
        ]);

        $value = CustomValue::findOrFail($id);
        $value->update($request->only('company_email', 'agent_name'));

        return redirect()
            ->route('admin.api.custom-values.index')
            ->with('success', 'Custom Value updated');
    }

    public function destroy($id)
    {
        $value = CustomValue::findOrFail($id);
        $value->delete();

        return redirect()->route('admin.api.custom-values.index')->with('success', 'Custom Value deleted');
    }

    public function apiIndex(Request $request)
    {
        $query = CustomValue::query();
        if ($request->has('email')) {
            $query->where('company_email', $request->email);
        }
        return response()->json($query->latest()->get());
    }
}