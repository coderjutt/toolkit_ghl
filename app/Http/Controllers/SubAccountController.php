<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\GhlUser;
class SubAccountController extends Controller
{
  protected $validation = [
        'name' => 'required',
        'email' => 'required|email|unique:users',
    ];
    protected $route = 'admin.user';
    protected $actions = [
        'add' => true,
        'edit' => true,
        'delete' => true,
        'status' => true,
        'loginwith' => true,
    ];

    protected $tobechanged = [
        'role' => [
            1 => 'Admin',
            2 => 'User',
        ],
        'status' => [
            1 => 'Active',
            0 => 'Inactive',
        ],
    ];

    // UserController.php

    public function index(Request $request)
    {

    if ($request->ajax() || $request->wantsJson()) {
        $data = User::select(['id', 'name', 'email', 'role', 'status','manual_key',  'password','location_id'])
        ->where('role', 2);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('role', function ($row) {
                return match ((int) $row->role) {
                    0 => 'Super Admin',
                    1 => 'Admin',
                    2 => 'Company',
                    default => 'User',
                };
            })
            ->editColumn('status', function ($row) {
                \Log::info($row);
                $statusText = $row->status ? 'Active' : 'Inactive';
                $btnClass = $row->status ? 'bg-green-600 hover:bg-green-700' : 'bg-yellow-500 hover:bg-yellow-600';
                $title = $row->status ? 'Currently Active - Click to Deactivate' : 'Currently Inactive - Click to Activate';

                return '<button type="button" data-id="' . $row->id . '"
                        class="status_changes text-white px-3 py-1 rounded ' . $btnClass . '"
                        title="' . $title . '">' . $statusText . '</button>';
            })
         ->addColumn('action', function ($row) {
            $id = $row->id;
            $name = htmlspecialchars($row->name, ENT_QUOTES);
            $email = htmlspecialchars($row->email, ENT_QUOTES);
            $manual_key = htmlspecialchars($row->manual_key, ENT_QUOTES);
            $location_id = htmlspecialchars($row->location_id, ENT_QUOTES);

    return '
        <div class="flex space-x-2">
                <button
                onclick="saveData(' . $id . ', \'' . $name . '\', \'' . $email . '\',\'' . $manual_key . '\', \'' . $location_id . '\')"   class="editModelOpen block text-white hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-blue-700 dark:focus:ring-blue-800" style="background-color: #001b4c;" type="button">
                Edit SubAccount
            </button>
            <a href="javascript:void(0);" data-id="' . $id . '"
                class="confirm-delete inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
                Delete
            </a>
            <a href="' . route('admin.user.autoLogin', $id) . '"
                class="rounded inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                title="Auto Login">
                SubAccount Login
            </a>
        </div>';
})

            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    return view('admin.subaccount.index');
}


    public function store(Request $req)
    {
        // dd($req->all());
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ];
        // Add password rule if creating a new user (id == 0)
        if ($req->id === '0') {
            $rules['password'] = 'required|string|min:8';
        }
        $validatedData = $req->validate($rules);
        if (!empty($req->password)) {
            $validatedData['password'] = bcrypt($req->password);
        }
         if (!empty($req->location_id)) {
            $validatedData['location_id'] = $req->location_id;
        }
        if ($req->id === '0') {
            $validatedData['role'] = is_role() == 'superadmin' ? 2 : 3;
            $validatedData['added_by'] = auth()->id();
        }
         if (!empty($req->manual_key)) {
                $manualKey = $req->manual_key;
                // $masterKey = env('MASTER_SSO_KEY');
                $masterSetting = Setting::where('key', 'crm_master_key')->first();
                $masterKey = $masterSetting ? $masterSetting->value : null;
                //  dd($masterKey);
                // Secure hash banate hain (non-reversible)
                $finalKey = Hash::make($masterKey . $manualKey);

                $validatedData['manual_key'] = $manualKey;
                $validatedData['final_key'] = $finalKey;
            }
        //dd($validatedData);
        // Save the user - update if the user already exists, or create a new one
        $user = User::updateOrCreate(['id' => $req->id], $validatedData);
        // Return a response
        if ($req->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'User saved successfully',
                'data' => $user,
            ]);
        }
    }

    public function status($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User status changed successfully',
            'new_status' => $user->status,
        ]);
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }


      public function policies(Request $request)
    {
        foreach($request->policies ?? [] as $key=>$value){

            save_Policies($key,$value);
        }
        return back();
    }

    public function searchUserByAjax(Request $request)
{


    $term = $request->q ?? $request->term ?? $request->search ?? '';

    $data = GhlUser::query()
        ->selectRaw("CONCAT(first_name, ' ', last_name) as text, user_id as id");
    if (!empty($term)) {
        $data->where(function ($query) use ($term) {
            $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"])
                  ->orWhere('id', $term);
        });
    }
    $results = $data->take(10)->get();
    return response()->json($results);
}
}
