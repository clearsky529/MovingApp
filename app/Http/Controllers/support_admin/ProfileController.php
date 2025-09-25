<?php

namespace App\Http\Controllers\support_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use DB;

class ProfileController extends Controller
{
    public function index()
    {
        $this->user= Auth::user();
        return view('theme.support-admin.user.profile',$this->data);
    }
}
