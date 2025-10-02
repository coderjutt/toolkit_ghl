<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    protected function redirectTo()
    {  
        // dd('hay');
        $user = auth()->user();

        if ($user->role == 1 || $user->role == 2) {
            return '/admin/dashboard';
        }

        return redirect()->back();
    }
    protected function authenticated(Request $request, $user)
    {
        $allowedModules = UserPermission::where('user_id', $user->id)
            ->pluck('module')
            ->unique()
            ->toArray();
            //   dd($allowedModules);

        session()->forget('user_modules_' . $user->id);
        session()->put('user_modules_' . $user->id, $allowedModules);
    }


  public function showLoginForm()
{
    // dd('hay');
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role == 1 || $user->role == 2) {
            return redirect('/admin/dashboard');
        }
    }
    return view('auth.login');
}

}
