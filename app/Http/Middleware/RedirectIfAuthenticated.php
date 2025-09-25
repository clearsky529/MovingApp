<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // if (Auth::guard($guard)->check()) {
        //     return redirect(RouteServiceProvider::HOME);
        // }
        // return $next($request);
        // dd('here');
        if (Auth::guard($guard)->check()) {
            // return redirect(RouteServiceProvider::HOME);
            $user = auth()->user();
            // dd($user);
            if($user->hasRole('super-admin')){
                return redirect(url('admin/home'));
            }
            elseif($user->hasRole('company-admin')){
                return redirect(url('company-admin/home'));
            }
            elseif($user->hasRole('support-admin')){
                return redirect(url('support-admin/home'));
            }
            else{
                return redirect('/');
            }
        }


        return $next($request);
    }
}
