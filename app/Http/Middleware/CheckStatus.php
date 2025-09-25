<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

         if(Auth::check() && Auth::user()->status != '1'){

            Auth::logout();

            $request->session()->flash('error', 'Your account has been put on hold. Please contact Kika support if you would like to reactivcate your account');

            return redirect('admin/login')->with('erro_login', 'Your error text');

        }
        return $response;
    }
}
