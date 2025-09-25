<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Subscription;
use App\CompanyType;
use App\Currencies;

class SubscriptionController extends Controller
{
    public function index()
    {
    	$this->subscriptions = Subscription::with('companyType','currency')->orderBy('id','desc')->get();
    	return view('theme.admin.subscriptions.index',$this->data);
    }

    public function show($id)
    {
        $id = \Crypt::decrypt($id);
    	$this->subscription = Subscription::with('companyType','currency')->where('id',$id)->first();
        return view('theme.admin.subscriptions.view', $this->data);
    }

    public function create()
    {
        $this->currencies = Currencies::get();
    	$this->companyTypes = CompanyType::get();
    	return view('theme.admin.subscriptions.create',$this->data);
    }

    public function store(Request $request)
    {
    	//set validation by fp
       if($request->title == 'Enterprise'){
            $this->validate($request, [
                'title'     => 'required|string|min:2|unique:subscriptions',
                'type'      => 'required',
                'status'    => 'required',
            ]); 
        }elseif($request->title == "Kika Direct"){
            if($request->type == 3)
            {
                if($request->monthly_subscription == ""){
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'type'                  => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'required',
                        'status'                => 'required',
                    ]);
                }else{
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'type'                  => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'numeric|min:1',
                        'status'                => 'required',
                    ]);
                }
                
            }
            else{
                $this->validate($request, [
                    'title'                 => 'required|string|min:2',
                    'type'                  => 'required',
                    'currency'              => 'required',
                    'monthly_subscription'  => 'required','numeric',
                    'status'                => 'required',
                ]); 
            }  
        }
        else{
            if($request->type == 3){
                if($request->monthly_subscription == "" || $request->free_users == "")
                {
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'type'                  => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'required',
                        'extra_user_month'      => 'required','numeric|min:1',
                        'free_users'            => 'required',
                        'status'                => 'required',
                    ]); 
                }else{
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'type'                  => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'numeric|min:1',
                        'extra_user_month'      => 'required','numeric|min:1',
                        'free_users'            => 'numeric|min:1',
                        'status'                => 'required',
                    ]); 
                }
            }
            else{
                $this->validate($request, [
                    'title'                 => 'required|string|min:2',
                    'type'                  => 'required',
                    'currency'              => 'required',
                    'monthly_subscription'  => 'required','numeric',
                    'extra_user_month'      => 'required','numeric|min:1',
                    'free_users'            => 'required','numeric',
                    'status'                => 'required',
                ]); 
            }
        }
        //end validation by fp
    	$subscription                      = new Subscription();
        $subscription->title               = $request->title;
    	$subscription->company_type        = $request->type;
        $subscription->currency_id         = $request->currency;
        $subscription->monthly_price       = $request->monthly_subscription;
    	$subscription->addon_price         = $request->extra_user_month;
        $subscription->free_users          = $request->free_users;
    	$subscription->status              = $request->status;
    	$subscription->created_by          = Auth::user()->id;

    	if ($subscription->save()) {
    		return redirect('admin/subscription')->with('flash_message_success', 'Subscription added successfully!');
    	}
    }

    public function edit($id)
    {
        $id = \Crypt::decrypt($id);
        $this->currencies = Currencies::get();
    	$subscription = Subscription::with('companyType')->where('id',$id)->first();
        $this->subscription = $subscription;
    	$subscription->validity = date('m/d/Y', strtotime($subscription->validity));
    	$this->companyTypes = CompanyType::get();
    	return view('theme.admin.subscriptions.edit',$this->data);
    }

    public function update(Request $request , $id)
    {
    	if($request->title == 'Enterprise'){
            $this->validate($request, [
                'title'             => 'required|string|min:2',
                'company_type'      => 'required',
                'status'            => 'required',
            ]); 
        }elseif($request->title == "Kika Direct"){
            if($request->company_type == 3)
            {
                if($request->monthly_subscription == ""){
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'company_type'          => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'required',
                        'status'                => 'required',
                    ]);
                }else{
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'company_type'          => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'numeric|min:1',
                        'status'                => 'required',
                    ]);
                }
                
            }
            else{
                $this->validate($request, [
                    'title'                 => 'required|string|min:2',
                    'company_type'          => 'required',
                    'currency'              => 'required',
                    'monthly_subscription'  => 'required','numeric',
                    'status'                => 'required',
                ]); 
            }  
        }
        else{
            if($request->company_type == 3){
                if($request->monthly_subscription == "" || $request->free_users == "")
                {
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'company_type'          => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'required',
                        'extra_user_month'      => 'required','numeric|min:1',
                        'free_users'            => 'required',
                        'status'                => 'required',
                    ]); 
                }else{
                    $this->validate($request, [
                        'title'                 => 'required|string|min:2',
                        'company_type'          => 'required',
                        'currency'              => 'required',
                        'monthly_subscription'  => 'numeric|min:1',
                        'extra_user_month'      => 'required','numeric|min:1',
                        'free_users'            => 'numeric|min:1',
                        'status'                => 'required',
                    ]); 
                }
            }
            else{
                $this->validate($request, [
                    'title'                 => 'required|string|min:2',
                    'company_type'          => 'required',
                    'currency'              => 'required',
                    'monthly_subscription'  => 'required','numeric',
                    'extra_user_month'      => 'required','numeric|min:1',
                    'free_users'            => 'required','numeric',
                    'status'                => 'required',
                ]); 
            }
        }
        //end validation by fp

    	$subscription = Subscription::where('id',$id)->first();

        $subscription->title               = $request->title;
        $subscription->company_type        = $request->company_type;
        $subscription->currency_id         = $request->currency;
        $subscription->monthly_price       = $request->monthly_subscription;
        $subscription->addon_price         = $request->extra_user_month;
        $subscription->free_users          = $request->free_users;
        $subscription->status              = $request->status;

    	if ($subscription->save()) {
    		return redirect('admin/subscription')->with('flash_message_success', 'Subscription updated successfully!');
    	}
    }

    public function delete($id)
    {
        $id = \Crypt::decrypt($id);
    	Subscription::where('id',$id)->delete();
    	return redirect('admin/subscription')->with('flash_message_success', 'Subscription deleted successfully!');
    }
}
