<?php
use App\Models\Setting;
use App\Models\GhlUser;
Use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
function supersetting($key, $default = '')
{

        $setting = Setting::where(['user_id' =>1, 'key' => $key])->first();

        $value = $setting->value ?? $default;
          return $value;
}
if (!function_exists('getActions')) {
    /**
     * Generate action buttons for DataTables
     *
     * @param array $actions
     * @param string $route
     * @param int $id
     * @return string
     */
    function getActions(array $actions, string $route, int $id)
    {
        $html = '';

        if (isset($actions['edit']) && $actions['edit']) {
            $editUrl = route($route . '.edit', ['user' => $id]);
            $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a> ';
        }

        if (isset($actions['delete']) && $actions['delete']) {
            $deleteUrl = route($route . '.destroy', ['user' => $id]);
            $html .= '<a href="' . $deleteUrl . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>';
        }

        return $html;
    }
}

//This Function give the user id of the Current logged in User
function isSuperAdmin()
{

    return (session('super_admin') && !empty(session('super_admin'))) || is_role() == 'admin'|| is_role() == 'company';
}

function login_id($id = "")
{
    if (!empty($id)) {
        return $id;
    }

    if (auth()->user()) {
        $id = auth()->user()->id;
    } elseif (session('uid')) {
        $id = session('uid');
    } elseif (Cache::has('user_ids321')) {
        $id = Cache::get('user_ids321');
    }

    return $id;
}

// function superAdmin()
// {
//    return 1;
// }
//This Function give the role of the Current logged in User
function is_role($user=null)
{
    if(!$user){
        if(auth()->user()){
            $user= auth()->user();
        }

    }
    if($user){
        if ($user->role == 1) {
            return 'super_admin';
        } else if ($user->role == 2) {
            return 'admin';
        } elseif ($user->role == 3) {
            return 'company';
        } else {
            return 'user';
        }
    }
       return null;
}

function save_settings($key, $value, $userid = null)
{
    \Log::info([$key, $value, $userid = null]);

    if (is_null($userid)) {
        $user_id = login_id();
    } else {
        $user_id = $userid;
    }
    $setting = Setting::updateOrCreate(
        ['key' => $key],
        [
            'value' => $value,
            'user_id' => $user_id,
            'key' => $key,
        ]
    );
    return $setting;
}



function save_Policies($key, $value, $userid = null)
{
    $user_id = $userid ?? login_id(); // Fallback to logged-in user

    \Log::info([
        'key' => $key,
        'value' => $value,
        'user_id' => $user_id,
    ]);

   $policies = Setting::updateOrCreate(
        ['key' => $key, 'user_id' => $user_id],
        ['value' => $value]
    );
     return $policies;
}


function loginUser()
{
    return auth()->user() ?? null;
}

function ghlUser($userData)
{
    try {
        return DB::transaction(function () use ($userData) {

            // Update or create the related User record
            $user = User::updateOrCreate(
                ['email' => $userData->email],
                [
                    'name' => $userData->firstName ?? null,
                    'password' => bcrypt($userData->firstName ?? 'password'),
                    'status' => 1,
                    'role' => 3,
                    'assigned_to' => login_id(),
                ]
            );

            // Update or create the GhlUser record
            $ghlUser = GhlUser::updateOrCreate(
                ['email' => $userData->email],
                [
                    'user_id' => $user->id,
                    'company_id' => $userData->companyId ?? null,
                    'ghl_user_id' => $userData->id ?? null,
                    'first_name' => $userData->firstName ?? null,
                    'last_name' => $userData->lastName ?? null,
                    'email' => $userData->email,
                    'phone' => $userData->phone ?? null,
                    'type' => $userData->roles->type ?? null,
                    'role' => $userData->roles->role ?? null,
                    'location_id' =>$userData->roles->locationIds[0] ?? null,
                    'permissions' => !empty($userData->permissions)
                        ? json_encode($userData->permissions)
                        : null,
                    'scopes' => !empty($userData->scopes)
                        ? json_encode($userData->scopes)
                        : null,
                    'scopes_assigned_to_only' => !empty($userData->scopesAssignedToOnly)
                        ? json_encode($userData->scopesAssignedToOnly)
                        : null,
                    'profile_photo' => $userData->profilePhotoUrl ?? null,
                ]
            );

            return $ghlUser;
        });
    } catch (\Exception $e) {
        Log::error('ghlUser helper failed', [
            'email' => $userData->email ?? 'unknown',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return null;
    }
}

if (!function_exists('formatMonetaryValue')) {
    function formatMonetaryValue($value) {
        if ($value >= 1000000) {
            return number_format($value / 1000000, 1) . 'M';
        }
        if ($value >= 1000) {
            return number_format($value / 1000, 1) . 'K';
        }
        return number_format($value, 2);
    }
}
