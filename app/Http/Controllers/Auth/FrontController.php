<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cms;

class FrontController extends Controller
{
  public function lending_slug($title)
  {   
    // dd('here');
      $slug = Cms::where('slug',$title)->first();
      // dd($slug);
      if($slug)
      {
          return view('auth.frontend.slug',compact('slug'));
      }
  }

  public function uplift_tutorial_video(){
    $slug = Cms::where('slug','uplifttutorialvideo')->first();
    return view('auth.frontend.uplifttutorialvideo',compact('slug'));
  }

  // public function lending_slug1($title1)
  // {   
  //   dd('here');
  //     $slug = Cms::where('slug',$title1)->first();
  //     // dd($slug);
  //     if($slug)
  //     {
  //         return view('auth.frontend.slug',compact('slug'));
  //     }
  // }

  // public function sub_title($id)
  // {
  //   dd($id);
  // }

  // public function url_pdf()
  // {
  //   $url = env('APP_URL');
  // }
}
