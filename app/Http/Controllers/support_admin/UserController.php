<?php

namespace App\Http\Controllers\support_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use App\User;
use App\Userrole;
use DB;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use File;
use App\States;
use App\Countries;
use App\Cities;
use App\Companies;
use App\CompanyUser;
use Validator;
use Redirect;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function EditProfile(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        if ($request->isMethod('post')) 
        {
            app()->setLocale(session()->get("locale"));
            $request->validate([
                'username'          => 'required|min:2|max:20',
                'email'             => 'required|email',
                'image'             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ],
            [
                'username.required' => trans('user.username.required'),
                'username.min'      => trans('user.username.min'),
                'username.max'      => trans('user.username.max'),
                'email.required'    => trans('user.email.required'),
                'email.email'       => trans('user.email.email'),
                'image.required'    => trans('user.image.required'),
                'image.mimes'       => trans('user.image.mimes'),
            ]
        );

            $users                   = User::findOrFail($user->id);
            $users->username            = $request->username;
            $users->email               = $request->email;
            // dd($request->hasFile('image'));
            if($request->hasFile('image')) {

                // $dirPath = 'public/user_image';
                // $publicPath = 'user_image';
                // $dir_path = public_path().'/'.$publicPath;
                // // dd(file_exists());
                // if (!file_exists($dir_path)) {
                //     File::makeDirectory(public_path().'/'.$publicPath,0777,true);
                // }
                // File::delete('public/user_image/'.$request->image);
                // $image = $request->file('image');
                // $name = time().'.'.$image->getClientOriginalExtension();
                
                // $destinationPath = public_path('user_image/');
                // // dd($destinationPath);
                // $image->move($destinationPath, $name);
                // $users->profile_pic                    = $name;
                $filename = User::where('id',$user->id)->value('profile_pic');
                // $exists = \Storage::has($filename);
           
                if(\Storage::has('/userprofile/'.$filename)){
                  \Storage::delete('/userprofile/'.$filename);
                }
                $image  = $request->file('image');
                $name   = time().'.'.$image->getClientOriginalExtension();
         
                 $file = $request->file('image');
                 $imageName=time().$file->getClientOriginalName();
                 $filePath = '/userprofile/' . $imageName;
                 \Storage::put($filePath, file_get_contents($request->file('image')));
                    $users->profile_pic = $imageName;
            } 
            $users->save();
            return redirect('/support-admin/home')->with('flash_message_success',trans('common.User profile updated successfully!'));
        }
        // return view('layouts.admin.pages.profile.edit',compact('user'));        
    }
}
