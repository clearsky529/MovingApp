<?php

namespace App\Http\Controllers\support_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\Admin\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;

class ChangePasswordController extends Controller
{
     public function index()
    {
        return view('theme.support-admin.user.changepassword');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        app()->setLocale(session()->get("locale"));
        $request->validate([
            'old_password'      => ['required','min:8','max:20', new MatchOldPassword],
            'new_password'      => ['required','min:8','max:20'],
            'confirm_password'  => ['required','min:8','max:20', 'same:new_password'],
        ],
        [
            'old_password.required' => trans('auth.old_password.required'),
            'old_password.min' => trans('auth.old_password.min'),
            'old_password.max' => trans('auth.old_password.max'),
            'new_password.required' => trans('auth.new_password.required'),
            'new_password.min' => trans('auth.new_password.min'),
            'new_password.max' => trans('auth.new_password.max'),
            'confirm_password.required' => trans('auth.confirm_password.required'),
            'confirm_password.min' => trans('auth.confirm_password.min'),
            'confirm_password.max' => trans('auth.confirm_password.max'),
        ]
    );
        
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
            
        return redirect('support-admin/changepassword')->with('flash_message_success',trans('common.User password change Successfully!'));
    }
}
