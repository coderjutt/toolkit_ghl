<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Models\CustomValue;

class CustomValueController extends Controller
{
    public function index()
    {
        $values = CustomValue::latest()->paginate(10);
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
        // dd($request);
        $request->validate([
            'name' => 'required|max:255',
            'value' => 'required|string|max:191',
        ]);
        CustomValue::create($request->only('name', 'value'));

        return redirect()
            ->route('admin.customvalue.index')
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
            'name' => 'required|max:255',
            'value' => 'required|string|max:191',
        ]);

        $value = CustomValue::findOrFail($id);
        $value->update($request->only('name', 'value'));

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
        $query = CustomValue::query();
        if ($request->has('email')) {
            $query->where('name', $request->email);
        }
        return response()->json($query->latest()->get());
    }
}