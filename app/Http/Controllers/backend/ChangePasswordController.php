<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\Admin\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;

class ChangePasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('theme.admin.user.changepassword');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            
        return redirect('admin/changepassword')->with('flash_message_success',trans('common.User password change Successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
