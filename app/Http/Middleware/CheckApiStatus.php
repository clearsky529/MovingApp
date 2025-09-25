<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\CompanyUser;

class CheckApiStatus
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

        if (Auth::check() && Auth::user()->status == 0) {

            $user = CompanyUser::where('user_id', Auth::user()->id)
                ->update(['is_login' => 0]);
            Auth::logout();

            $response = [
                "status" => 0,
                "message" => 'Your account has been put on hold. Please contact Kika support if you would like to reactivcate your account'
            ];

            return response()->json($response);
        }
        return $response;
    }
}
