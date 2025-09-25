<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{UserSubscription,User,Subscription,Companies,StripePayment,Move,Currencies,Constant};
use Carbon\Carbon;
use Stripe;
use Config;
use Illuminate\Support\Facades\Mail;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Log;

class StripeTestController extends Controller
{
    public function test(Request $request)
    {
        $date = Carbon::now();
        $current_date = $date->toDateString();
        $users = UserSubscription::with('subscription')
        ->whereHas('subscription', function($q){
            $q->where('title','!=','Kika Direct');
            $q->orWhere('title','!=','Enterprise');
        })
        ->where('status','=',1)
        ->where('validity',$current_date)
        ->get();
        // dd($users);
        foreach($users as $user)
        {
            try{
                $date = Carbon::now();
                $current_date = $date->toDateString();
                $validity = $user->validity;
                $total_icr_amount = '';
                $payableAmount = '';
                if($current_date == $validity)
                {
                    $company  = Companies::where('tbl_users_id',$user->user_id)->first();

                   $total_company_moves_count = Move::where('company_id',$company->id)->where('is_completed_icr_uplift','=',1)->where('is_completed_icr_delivery','=',1)->where('deleted_at','=',null)->whereMonth('created_at', Carbon::now()->month)->count();
                   // dd($total_company_moves_count);
                    Stripe\Stripe::setApiKey(array_key_exists('STRIPE_SECRET', $_SERVER) ? $_SERVER['STRIPE_SECRET'] : env("STRIPE_SECRET"));
                    
                        // //create price in stripe account
                        // $price_amount = $user->final_price*100;
                        
                    $user_details = User::where('id',$user->user_id)->first();
                    $latest_subscription = UserSubscription::where('user_id',$user_details->id)->latest()->first();
                    $subscription = Subscription::where('id',$latest_subscription->subscription_id)->value('title');
                    if($subscription == "Kika Direct"){
                        if($total_company_moves_count > 0){
                            $icr_details = Constant::where('name','=','icr_price')->first();
                            $icr_price = $icr_details->value;
                            $total_icr_amount = $icr_price*$total_company_moves_count;
                            $subscriptionPlan = $user->final_price;
                            $total_amount = $total_icr_amount + $subscriptionPlan;
                            $payableAmount = $total_amount*100;
                            // dd($payableAmount);
                        }else{
                            $payableAmount = $user->final_price*100;
                        }    
                    }else{
                        $payableAmount = $user->final_price*100;
                    }   
                    $client_id  = array_key_exists('STRIPE_SECRET', $_SERVER) ? $_SERVER['STRIPE_SECRET'] : env(('STRIPE_SECRET'));
                    $stripe     = new \Stripe\StripeClient($client_id);
                    $stripeUser = StripePayment::where('user_id',$user_details->id)->latest()->first();
                
                    // $exchangeRates = new ExchangeRate();
                    // $payableAmount = (int)$exchangeRates->convert((int)$user->final_price, strtoupper('INR'), Config::get('constants.currency'), Carbon::now())*100;
                    $currencydata = Currencies::where('id',$user->currency_code)->first();
                    
                    
                        $intent_update =   Stripe\Charge::create ([
                                "amount" => $payableAmount,
                                "currency" => $currencydata->currency_code,
                                "customer" =>$user_details->stripe_id,
                                "description" => "kika update subscription plan" 
                        ]);
                        if($intent_update->status == "succeeded")
                        {
                            $payment                         = new StripePayment();
                            $payment->user_id                = $user_details->id;
                            $payment->payment_id             = $intent_update['id'];
                            $payment->amount                 = $intent_update['amount'];
                            $payment->customer_name          = User::where('id',$user_details->id)->value('email');
                            $payment->application_fee        = $intent_update['application_fee_amount'];
                            $payment->cancellation_reason    = $intent_update['cancellation_reason'];
                            $payment->client_secret          = $intent_update['balance_transaction'];
                            $payment->description            = $intent_update['description'];
                            $payment->payment_method         = $intent_update['payment_method'];
                            $payment->status                 = $intent_update['status'];
                            $payment->response_array         = json_encode($intent_update);
                            $payment->save();

                            $update_intent_id = User::where('id',$user_details->id)->update(['intent_id' => $intent_update->id]);
                        
                            // $subscription = Subscription::findOrFail($user_details->subscription_id);
                           
                        
                            $subscription = Subscription::findOrFail($latest_subscription->subscription_id);
                            UserSubscription::where('user_id',$user_details->id)->where('subscription_id',$user->subscription_id)->update(['status' => 0]);


                            $new_subscription = new UserSubscription();
                            $new_subscription->subscription_id    = $subscription->id;
                            $new_subscription->payment_id         = $payment->id;
                            $new_subscription->user_id            = $user_details->id;
                            $new_subscription->validity           = date('Y-m-d', strtotime('+1 month'));
                            $new_subscription->currency_code      = $subscription->currency_id;
                            $new_subscription->subscription_price = $subscription->monthly_price;
                            $new_subscription->addon_unit_price   = $subscription->addon_price;
                            $new_subscription->addon_user         = null;
                            $new_subscription->final_price        = $latest_subscription->final_price;
                            $new_subscription->total_icr_price    = $total_icr_amount;
                            $new_subscription->status             = 1;
                            $new_subscription->success_payment_status = 1;
                            $new_subscription->save();

                            Companies::where('tbl_users_id',$user_details->id)->update(['subscription_id' => $user->subscription_id]);

                            $kika_id = Companies::where('tbl_users_id',$user_details->id)->value('kika_id');
                            $user_id = $payment['user_id'];
                            $user    = User::where('id',$user_id)->first();
                
                            $user_data = UserSubscription::where('user_id',$user->id)->latest()->first(); 
                            $current_code = Currencies::where('id',$user_data->currency_code)->first();
                            $amount = $payableAmount;
                            $currency_code = $current_code['currency_symbol'];
                            $data    = array('id'=>$user->id, 'kika_id' => $kika_id, 'amount' => $amount, 'currency_code' => $currency_code);

                            // echo "<pre>";
                            // print_r($user_data);


                            Mail::send('mails.stripenotificationMail', $data, function($message) use($user) {
                                $message->to($user->email)->subject('Kika deduct payment');
                                $message->from('test.vpninfotech@gmail.com','VPN Infotech');
                            });

                        }
                    
                    }
                }catch (\Exception $e) {
                Log::info($e->getMessage());
                }
            }  
        }

    }

