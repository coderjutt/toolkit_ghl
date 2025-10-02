<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
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
            $authUser = auth()->user();

            $data = User::select(['id', 'name', 'email', 'role', 'status', 'password', 'location_id'])
                ->whereNotIn('role', [1, 2])
                ->where('assigned_to', $authUser->id);
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
                    $location_id = htmlspecialchars($row->location_id, ENT_QUOTES);

                    return '
        <div class="flex space-x-2">
                <button
                onclick="saveData(' . $id . ', \'' . $name . '\', \'' . $email . '\', \'' . $location_id . '\')"  class="editModelOpen block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Edit User
            </button>
            <a href="javascript:void(0);" data-id="' . $id . '"
                class="confirm-delete inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
                Delete
            </a>
            <a href="' . route('admin.user.autoLogin', $id) . '"
                class="rounded inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                title="Auto Login">
                User Login
            </a>
        </div>';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.users.index');
    }


    // public function store(Request $req)
    // {
    //     //dd($req->all());
    //     $rules = [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //     ];
        
    //     // Add password rule if creating a new user (id == 0)
    //     if ($req->id === '0') {
    //         $rules['password'] = 'required|string|min:8';
    //     }
    //     $validatedData = $req->validate($rules);
    //     if (!empty($req->password)) {
    //         $validatedData['password'] = bcrypt($req->password);
    //     }
    //     if (!empty($req->location_id)) {
    //         $validatedData['location_id'] = $req->location_id;
    //     }
    //     if ($req->id === '0') {
    //         $validatedData['role'] = is_role() == 'superadmin' ? 2 : 3;
    //         $validatedData['added_by'] = auth()->id();
    //     }
    //     //dd($validatedData);
    //     // Save the user - update if the user already exists, or create a new one
    //     $user = User::updateOrCreate(['id' => $req->id], $validatedData);
    //     // Return a response
    //     if ($req->ajax()) {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'User saved successfully',
    //             'data' => $user,
    //         ]);
    //     }
    // }



    

    // use Illuminate\Support\Facades\Hash;

        public function store(Request $req)
        {
            // dd($req->all());
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ];
            // Add password rule if creating a new user
            if ($req->id === '0') {
                $rules['password'] = 'required|string|min:8';
            }
            // Manual key rule (optional if user update kare to bhi chal jaye)
            $rules['manual_key'] = 'nullable|string|max:255';

            $validatedData = $req->validate($rules);
            // Encrypt password if provided
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
            // ✅ Manual Key + Master Key se Final Key generate karna
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
            // dd($validatedData);

            // Save user (update or create)
            $user = User::updateOrCreate(['id' => $req->id], $validatedData);
            // Response
            if ($req->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User saved successfully',
                    'data' => $user,
                ]);
            }
        }

    public function profile()
    {
        $user = Auth::user();

        return view('admin.users.userprofile', get_defined_vars());
    }

    public function changePassword(Request $req)
    {
        //dd($req->all());
        $user = Auth::user();
        $check = Validator::make($req->all(), [
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        if ($check->fails()) {
            return redirect()
                ->back()
                ->with('error', $check->errors()->first());
        }
        $user->password = bcrypt($req->password);
        $user->save();
        return redirect()->back()->with('success', 'Password updated successfully!');

    }

    public function changeEmail(Request $request)
    {

        $check = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required|min:8',
        ]);

        if ($check->fails()) {
            return redirect()
                ->back()
                ->with('error', $check->errors()->first());
        }
        $user = Auth::user();
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Email updated Successfully!');
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
    public function general(Request $request)
    {
        $request->validate([
            'first-name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->input('first-name'),
            'email' => $request->input('email'),
            'status' => $request->input('status'),
        ]);

        return back();
    }

    public function imageUpload(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }
            $image = $request->file('image');
            $imageName = 'profile_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profile'), $imageName);
            $user->image = 'uploads/profile/' . $imageName;
            $user->save();
        }

        return redirect()->back()->with('success', 'Image updated successfully!');
    }

}
