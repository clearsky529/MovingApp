<?php

namespace App\Http\Controllers\support_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cms;

class CmsController extends Controller
{
    public function index()
    {
      $list = Cms::get();
      return view('theme.support-admin.cms.index',compact('list'));
    }

    public function create()
    {
    	return view('theme.support-admin.cms.create');
    }

    public function store(Request $request)
    {
      $this->validate($request, [
        'title'           => 'required|min:2|string|unique:cms,title',
        'description'     => 'required|min:2',
        'status'          => 'required',
        'field_status'    => 'required',
      ]);

      $cms = new Cms();
      $cms->title = $request->title;
      $cms->slug  = $request->slug;
      $cms->description = $request->description;
      $cms->status = $request->status;
      $cms->field_status = $request->field_status;
      $cms->save();
      return redirect('support-admin/cms')->with('flash_message_success',trans('common.User profile updated successfully!'));
    }

    public function show($id)
    {
      $id = \Crypt::decrypt($id);
      $this->cms = Cms::where('id',$id)->first();
      return view('theme.support-admin.cms.view',$this->data);
    
    }

    public function edit($id)
    {
      $id = \Crypt::decrypt($id);
      $this->cms = Cms::where('id',$id)->first();
      return view('theme.support-admin.cms.edit',$this->data);
    }

    public function update(Request $request , $id)
    {
      // dd('here');
      // dd(json_encode($request->description));
        

        $cms = Cms::where('id',$id)->first();
        $cms->title = $request->title ? $request->title : $cms->title;
        $cms->slug  = $request->slug ? $request->slug : $cms->slug;
        $cms->description = $request->description ? $request->description : $cms->description;
        $cms->status = $request->status ? $request->status : $cms->status;
        $cms->field_status = $request->field_status ? $request->field_status : $cms->status;
        $cms->save();
      // $companyUser = CompanyUser::where('id',$id)->first();
        // $companyUser->name        = $request->full_name;
        // $companyUser->phone       = $request->phone;
        //  $companyUser->save();         

      return redirect('support-admin/cms')->with('flash_message_success', 'Device updated successfully!');
    }

  public function vpnPhpInfo()
  {
    phpinfo();
  }
}
