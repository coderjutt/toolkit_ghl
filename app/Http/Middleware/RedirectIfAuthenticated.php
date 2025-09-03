<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    //  public function handle(Request $request, Closure $next, ...$guards)
    // {
    //     foreach ($guards as $guard) {
    //         if (Auth::guard($guard)->check()) {
    //             // 👇 Redirect logged-in users to dashboard
    //             return redirect('/admin/dashboard');
    //         }
    //     }

    //     return $next($request);
    // }


    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();

            // Redirect based on role
            if ($user->role == 1) {
                return redirect('/admin/dashboard'); // Super Admin
            } elseif ($user->role == 2) {
                return redirect('/admin/dashboard'); // Admin
            }

            // return response('yes'); // fallback
        }
         return redirect('/login');
        // return $next($request);
    }

}
