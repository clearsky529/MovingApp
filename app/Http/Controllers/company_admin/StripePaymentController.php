<?php

namespace App\Http\Controllers\company_admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Rules\Admin\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Stripe;
use App\User;
use App\Move;
use App\CompanyUser;
use App\Companies;
use App\Currencies;
use App\StripePayment;
use App\UserPlanAddon;
use App\UserSubscription;
use App\{Subscription,CompanyAgent};
use Session;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use App\Helpers\CompanyAdmin;



class StripePaymentController extends Controller
{
    public function stripePayment(Request $request)
    {
    	if(Session::get('company-admin')){
	        $user = Session::get('company-admin');
      	}
      	elseif(Auth::user() != null){
	        $user = Auth::user()->id;
      	}

        try 
       	{
			Stripe\Stripe::setApiKey(array_key_exists('STRIPE_SECRET', $_SERVER) ? $_SERVER['STRIPE_SECRET'] : env("STRIPE_SECRET"));
			$exchangeRates = new ExchangeRate();
			$payableAmount = (int)$exchangeRates->convert($request->amount*100,strtoupper($request->currency),strtoupper($request->currency), Carbon::now());
			// dd($payableAmount);
			// $payableAmount = (int)$exchangeRates->convert((int)$request->amount, strtoupper($request->currency), Config::get('constants.currency'), Carbon::now())*100;
			// dd($payableAmount);

      		// Create new PaymentIntent with a PaymentMethod ID from the client.
      		// $payableAmount = $payableAmount1;

      		// $payableAmount = $request->amount*100;
      		// dd($payableAmount);
			$client_id  = array_key_exists('STRIPE_SECRET', $_SERVER) ? $_SERVER['STRIPE_SECRET'] : env(('STRIPE_SECRET'));
			$stripe 	= new \Stripe\StripeClient($client_id);
				
			$register_user_details = User::where('id',$request->id)->first();
			$register_company = Companies::where('tbl_users_id',$register_user_details->id)->first();
					
			//create customer
			$customer = \Stripe\Customer::create([
				'source' => $request->stripeToken,
				'email' => $register_user_details->email,
				'name' =>  $register_company->name
			]);

			//create charge
			$intent = Stripe\Charge::create ([
		                "amount" => $payableAmount,
		                "currency" => $request->currency,
		                // "source" => $request->stripeToken,
		                "customer" =>$customer->id,
		                "description" => "kika subscription plan" 
		    ]);
				
			$subscription_register_user = Subscription::findOrFail($request->subscription);
			$client_id  = array_key_exists('STRIPE_SECRET', $_SERVER) ? $_SERVER['STRIPE_SECRET'] : env(('STRIPE_SECRET'));
			$stripe 	= new \Stripe\StripeClient($client_id);
			User::where('id',$request->id)->update(['stripe_id' => $customer->id]);

            if ($intent['status'] == "succeeded") 
            {
              	$paymentFor = Session::get('payment_for');

              	if ($paymentFor == "addon")
              	{
					$payment                         = new StripePayment();
					$payment->user_id                = Auth::user()->id;
					$payment->payment_id             = $intent['id'];
					$payment->amount                 = $intent['amount'];
					$payment->customer_name          = Auth::user()->email;
					$payment->application_fee        = $intent['application_fee_amount'];
					$payment->cancellation_reason    = $intent['cancellation_reason'];
					$payment->client_secret          = $intent['balance_transaction'];
					$payment->description            = $intent['description'];
					$payment->payment_method         = $intent['payment_method'];
					$payment->status                 = $intent['status'];
					$payment->response_array         = json_encode($intent);
					$payment->save();

					// code by fp
					$intent_id = User::where('id',$request->id)->update(['intent_id' => $intent->id]);
					//end code by fp
					$company_user_details = User::where('id',$user)->first();
					$UserSubscription = UserSubscription::where('user_id',$user)
													->where('subscription_id',$company_user_details->company->subscription->id)
													->where('status',1)
													->increment('addon_user',$request->addon);

					$planAddon                  = new UserPlanAddon();
					$planAddon->user_id         = $user;
					$planAddon->subscription_id = $company_user_details->company->subscription->id;
					$planAddon->payment_id      = $payment->id;
					$planAddon->user_addon      = $request->addon;
					$planAddon->save();
				}
				elseif ($paymentFor == "extend_plan" || $paymentFor == "new_plan") 
				{
					if(isset($user) != "")
					{
						$company_id  = CompanyAdmin::getCompanyId();
						$last_sub_name = Subscription::where('id',$request->subscription)->value('title');
						$latest_subscription  = UserSubscription::where('user_id',$user)->latest()->first(); 
						if($latest_subscription !== null){
							$sub = Subscription::where('id',$latest_subscription->subscription_id)->value('title');
						}else{
							$company_id  = CompanyAdmin::getCompanyId();
							$sub = Companies::where('tbl_users_id',$company_id)->value('tbl_users_id');
						}

						if($sub == "Kika Direct")
						{
							$existing_company_device = CompanyUser::where('company_id',$company_id)->get();
							foreach($existing_company_device as $device){
								User::where('id',$device->user_id)->delete();
							}
							$deleteUser = CompanyUser::where('company_id',$company_id)->delete();
							if($deleteUser){
								Session::put('deleteUser','deleteUser');
							}
						}	
					}
					$payment                         = new StripePayment();
          	        $payment->user_id                = $request->id ;
          	        $payment->payment_id             = $intent['id'];
          	        $payment->amount                 = $intent['amount'];
          	        $payment->customer_name          = User::where('id',$request->id)->value('email');
          	        $payment->application_fee        = $intent['application_fee_amount'];
          	        $payment->cancellation_reason    = $intent['cancellation_reason'];
          	        $payment->client_secret          = $intent['client_secret'];
          	        $payment->description            = $intent['description'];
          	        $payment->payment_method         = $intent['payment_method'];
          	        $payment->status                 = $intent['status'];
          	        $payment->response_array         = json_encode($intent);
          	        $payment->save();

					 // code by fp
					// $intent_id = User::where('id',$request->id)->update(['intent_id' => $intent->id]);
					//end code by fp
					

					UserSubscription::where('user_id',$request->id)->update(['status' => 0]);

					Companies::where('tbl_users_id',$request->id)->update(['subscription_id' => $request->subscription]);

          	        $subscription = Subscription::findOrFail($request->subscription);
          	        // dd($subscription);

          	        $new_subscription = new UserSubscription();
          	        $new_subscription->subscription_id    = $request->subscription;
          	        $new_subscription->payment_id         = $payment->id;
          	        $new_subscription->user_id            = $request->id;
          	        $new_subscription->validity           = date('Y-m-d', strtotime('+1 month'));
          	        $new_subscription->currency_code      = $subscription->currency_id;
          	        $new_subscription->subscription_price = $subscription->monthly_price;
          	        $new_subscription->addon_unit_price   = $subscription->addon_price;
          	        $new_subscription->addon_user         = $request->addon == null ? 0 : $request->addon;
          	        $new_subscription->final_price        = $request->amount;
          	        $new_subscription->status             = 1;
          	        $new_subscription->success_payment_status = 0;
          	        $new_subscription->save();

          	       	if(isset($user) != "")
          	       	{
	                	$subcription_get = Subscription::where('id',$request->subscription)->where('title','=','Kika Direct')->first();
	                    if($subcription_get)
	                    {
	                      	$company_update = Companies::where('tbl_users_id',$latest_subscription->user_id)
	                                        ->update([
	                                          'subscription_id' => $request->subscription,
	                                          'kika_direct' => 1
	                                        ]);
						  	$kika_direct_company = Companies::where('tbl_users_id',$user)->first();
						    $update_agent = CompanyAgent::where('kika_id',$kika_direct_company->kika_id)->update(['is_kika_direct' => 1]);
	                        $user_update = User::where('id',$latest_subscription->user_id)->update(['kika_direct' => 1]);
	                    }
	                    else
	                    {
		                    if($latest_subscription == "")
		                    {
		                    	$latest_user_id = $request->id;
		                    	$company_update = Companies::where('tbl_users_id',$latest_user_id)
		                                        ->update([
		                                          'subscription_id' => $request->subscription,
		                                          'kika_direct' => 0
		                                        ]);
		                        $kika_direct_company = Companies::where('tbl_users_id',$user)->first();
								$update_agent = CompanyAgent::where('kika_id',$kika_direct_company->kika_id)->update(['is_kika_direct' => 0]);
		                      	$user_update = User::where('id',$latest_user_id)->update(['kika_direct' => 0]);
		                    }
		                    else
		                    {
		                    	$company_update = Companies::where('tbl_users_id',$latest_subscription->user_id)
		                                        ->update([
		                                          'subscription_id' => $request->subscription,
		                                          'kika_direct' => 0
		                                        ]);
		                        $kika_direct_company = Companies::where('tbl_users_id',$user)->first();
								$update_agent = CompanyAgent::where('kika_id',$kika_direct_company->kika_id)->update(['is_kika_direct' => 0]);
		                      	$user_update = User::where('id',$latest_subscription->user_id)->update(['kika_direct' => 0]);
		                    }
	                    }
	          	    }
					if($paymentFor == "new_plan")
					{
						$kika_id = Companies::where('tbl_users_id',$request->id)->value('kika_id');
						$user_id = $payment['user_id'];
						$user 	 = User::where('id',$user_id)->first();
						$data    = array('id'=>$user->id, 'kika_id' => $kika_id);
			
						$user_data = User::where('id',$user->id)->first();  
		
						Mail::send('mails.ActivationMail', $data, function($message) use($user_data) {
							$message->to($user_data->email)->subject('Welcome To Kika');
							$message->from('test.vpninfotech@gmail.com','VPN Infotech');
						});

						if(count(Mail::failures()) > 0){
							session::flash('error','Mail not sent!!');
						}
						else{
							session::flash('success','Mail sent successfully!!');
						}
					}
	            }
            }
            $output = $this->generateResponse($intent, $request->addon);

            echo json_encode($output);
        } 
        catch (\Stripe\Error\Card $e) 
        {
            echo json_encode([
              'error' => $e->getMessage()
            ]);
        }
    }

    public function generateResponse($intent, $addon = null)
    {
	  	switch($intent['status']) {
			case "requires_action":
	      	case "requires_source_action":
	        	// Card requires authentication
	            return [
	              'requiresAction'=> true,
	              'paymentIntentId'=> $intent['id'],
	              'clientSecret'=> $intent['client_secret']
	            ];
          	case "requires_payment_method":
         	case "requires_source":
            	// Card was not properly authenticated, suggest a new payment method
            	return [
              		'error' => "Your card was denied, please provide a new payment method"
            	];
          	case "succeeded":
                // Payment is complete, authentication not required
                // To cancel the payment after capture you will need to issue a Refund (https://stripe.com/docs/api/refunds)
                // $request->session()->put('flash_message_success', 'Your user limit have been extended.');

      		switch (Session::get('payment_for')) {
      			case 'addon':
      				Session::flash('flash_message_success', 'Your user limit have been extended.');
      				break;
      			
      			case 'extend_plan':
							// if(Session::get('deleteUser'))
							// {
       //        					Session::flash('flash_message_success', 'Your subscription plan is successfully updated. Changing your subscription plan to Kika Direct will delete all devices from your account. You will need to recreate the devices after you change plans.');
							// }else{
			Session::flash('flash_message_success', 'Your subscription plan is successfully updated.');
							// }
              	break;
            }

          	Session::forget('payment_for');
			Session::forget('id');
			Session::forget('amount');
			Session::forget('currency');
			Session::forget('subscriptionID');
			Session::forget('addon');
			Session::forget('fetchPublishableUrl');
			Session::forget('paymentUrl');
			Session::forget('redirectUrl');
			Session::forget('deleteUser');

            return [
            	'add_on' => $addon
            ];
        }
    }
}
