<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Session;
use Auth;

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

    use AuthenticatesUsers {
        logout as performLogout;
    }
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {

        $user = auth()->user();

        if($user->hasRole('super-admin'))
        {
                // return redirect(route('home'));
            return 'admin/home';
        }
        elseif($user->hasRole('company-admin'))
        {
            return 'company-admin/home';
        }
        elseif($user->hasRole('support-admin'))
        {
                // return redirect(route('home'));
            return 'support-admin/home';
        }
        else{
          return 'admin/login';
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest','checkstatus'])->except('logout');
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect('/admin/login');
    }
}
