<?php
namespace App\Http\Controllers\company_admin;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Helpers\CompanyAdmin;
use App\Rules\ReferralCode;
use App\UserSubscription;
use App\StripePayment;
use App\Subscription;
use App\CompanyType;
use App\Currencies;
use App\{Companies,CompanyAgent};
use App\Countries;
use App\States;
use App\Cities;
use App\User;
use Stripe\Error\Card;
use Session;
use Keygen;
use Stripe;
use Crypt;
use DB;

class RegisterController extends Controller
{

    public function termscondition()
    {
        return view('auth/privacy-policy');
    }

    public function contactus()
    {
        return view('auth/contact-us');
    }

    public function index(Request $request)
    {
      $this->companyTypes  = CompanyType::get();
      $this->referral_code = $request->referral_code ?  $request->referral_code :  null;

    	return view('auth.company-admin.register-step-1',$this->data);
    }

    public function registerStep1(Request $request)
    {
      $request->website = strpos($request->website, 'http') !== 0 ? "http://$request->website" : $request->website;

    	$this->validate($request, [
        'company_name'  => 'required|string|min:2',
        'type'          => 'required',
        'email'         => 'required|email|unique:users',
        'password'      => 'required|min:8',
        'website'       => 'required',
        'referral_code' => new ReferralCode,
    	]); 

      if($request->type != "3" && !filter_var($request->website, FILTER_VALIDATE_URL)) {
          throw ValidationException::withMessages(['website' => 'URL format is not correct.']);
      }

      $referral_code = $this->gererateReferralCode($request->company_name);
      
      $request->session()->put('type', $request->type);
    	$request->session()->put('company_name', $request->company_name);
    	$request->session()->put('email', $request->email);
    	$request->session()->put('password', bcrypt($request->password));
    	$request->session()->put('website', $request->website);
      $request->session()->put('referral_code', $referral_code);
      $request->referral_code != null ? $request->session()->put('is_referral_applied', true) : $request->session()->put('is_referral_applied', false);
      $request->referral_code != null ? $request->session()->put('referred_code', $request->referral_code) : '';

      $companyTypes = CompanyType::get();
      $countries = Countries::all();

      return redirect()->route('company-admin.register.step-2');
    }

    public function formStep2()
    {
      
      $this->countries = Countries::all();

      return view('auth.company-admin.register-step-2',$this->data);
    }

    public function registerStep2(Request $request)
    {
      $this->validate($request, [
        'contact_name'   => 'required|string|min:2',
        'contact_number' => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
        'country'        => 'required',
        'state'          => 'required',
        'city'           => 'required',
      ]); 

      if (!$request->session()->has('email')) {
        return redirect('/admin/login');
      }

      $request->session()->put('contact_name', $request->contact_name);
      $request->session()->put('contact_number', $request->contact_number);
      $request->session()->put('country', $request->country);
      $request->session()->put('state', $request->state);
      $request->session()->put('city', $request->city);

      return redirect()->route('company-admin.register.step-3');

    }

    public function formStep3(Request $request)
    {
      $type_id = $request->session()->get('type');

      if(CompanyType::where('id',$type_id)->exists())
      {
        $request_company_type = CompanyType::where('id',$type_id)->first();
        if($request_company_type->company_type == "Moving")
        {
          $this->subscriptions = Subscription::with('currency')
                  ->where('company_type',$type_id)
                  ->where('status',1)
                  ->where('title','=','Enterprise')
                  ->get();
        }else
        {
           $this->subscriptions = Subscription::with('currency')
                  ->where('company_type',$type_id)
                  ->where('status',1)
                  ->get();
        }
      }
      return view('auth.company-admin.register-step-3',$this->data);
    }

    public function register(Request $request)
    {
      $this->validate($request, [
        'subscription'    => 'required',
        'addon'           => 'sometimes|nullable|numeric|min:0',
      ]);

      if (!$request->session()->has('email')) {
        return redirect('/admin/login');
      }

      $kika_direct =  Subscription::where('id',$request->subscription)
                    ->where('title','=','Kika Direct')
                    ->first();

      $user = new User();
      $user->role_id  = 2;
      $user->email    = $request->session()->get('email');
      $user->status   = 0;
      $user->password = $request->session()->get('password');

      if($kika_direct != null){
        $user->kika_direct = 1;
      }
      else{
        $user->kika_direct = 0;
      }
      $user->save();

      $user->assignRole('company-admin');

      $kika_id = $this->getKikaID($request->session()->get('type'));

      $type_id = $request->session()->get('type');

      $subscription_plan =  Subscription::where('id',$request->subscription)->first();
            // $enterprise_plan =  Subscription::where('id',$request->subscription)
            //         ->where('title','=','Enterprise')
            //         ->first();

      if($request->subscription == null)
      {
          $company = new Companies();
          $company->tbl_users_id     = $user->id;
          $company->kika_id          = $kika_id;
          $company->name             = $request->session()->get('company_name');
          $company->email            = $request->session()->get('email');
          $company->website          = $request->session()->get('website');
          $company->contact_name     = $request->session()->get('contact_name');
          $company->contact_number   = $request->session()->get('contact_number');
          $company->city             = $request->session()->get('city');
          $company->state            = $request->session()->get('state');
          $company->country          = $request->session()->get('country');
          $company->type             = $request->session()->get('type');
          $company->subscription_id  = null;
          $company->kika_direct      = 0;
          $company->referred_by      = $request->session()->get('is_referral_applied');
          $company->referral_code    = $request->session()->get('referral_code');
          $company->free_trial_day   = CompanyAdmin::getFreeTrialDay();
      }
      elseif($request->subscription == '0')
      {
          $company = new Companies();
          $company->tbl_users_id     = $user->id;
          $company->kika_id          = $kika_id;
          $company->name             = $request->session()->get('company_name');
          $company->email            = $request->session()->get('email');
          $company->website          = $request->session()->get('website');
          $company->contact_name     = $request->session()->get('contact_name');
          $company->contact_number   = $request->session()->get('contact_number');
          $company->city             = $request->session()->get('city');
          $company->state            = $request->session()->get('state');
          $company->country          = $request->session()->get('country');
          $company->type             = $request->session()->get('type');
          $company->subscription_id  = null;
          $company->kika_direct      = 0;
          $company->referred_by      = $request->session()->get('is_referral_applied');
          $company->referral_code    = $request->session()->get('referral_code');
          $company->free_trial_day   = CompanyAdmin::getFreeTrialDay();
      }
      elseif($subscription_plan->title == "Enterprise")
      {
        $company = new Companies();
        $company->tbl_users_id     = $user->id;
        $company->kika_id          = $kika_id;
        $company->name             = $request->session()->get('company_name');
        $company->email            = $request->session()->get('email');
        $company->website          = $request->session()->get('website');
        $company->contact_name     = $request->session()->get('contact_name');
        $company->contact_number   = $request->session()->get('contact_number');
        $company->city             = $request->session()->get('city');
        $company->state            = $request->session()->get('state');
        $company->country          = $request->session()->get('country');
        $company->type             = $request->session()->get('type');
        $company->kika_direct      = 0;
        $company->subscription_id  = $subscription_plan->id ? $subscription_plan->id : $request->subscription;
        $company->referred_by      = $request->session()->get('is_referral_applied');
        $company->referral_code    = $request->session()->get('referral_code');
        $company->free_trial_day   = CompanyAdmin::getFreeTrialDay();
      }
      elseif($subscription_plan->title == "Kika Direct")
      {
        $company = new Companies();
        $company->tbl_users_id     = $user->id;
        $company->kika_id          = $kika_id;
        $company->name             = $request->session()->get('company_name');
        $company->email            = $request->session()->get('email');
        $company->website          = $request->session()->get('website');
        $company->contact_name     = $request->session()->get('contact_name');
        $company->contact_number   = $request->session()->get('contact_number');
        $company->city             = $request->session()->get('city');
        $company->state            = $request->session()->get('state');
        $company->country          = $request->session()->get('country');
        $company->type             = $request->session()->get('type');
        $company->kika_direct      = 1;
        $company->subscription_id  = $request->subscription;
        $company->referred_by      = $request->session()->get('is_referral_applied');
        $company->referral_code    = $request->session()->get('referral_code');
        $company->free_trial_day   = CompanyAdmin::getFreeTrialDay();
      }
      else
      {
        $company = new Companies();
        $company->tbl_users_id     = $user->id;
        $company->kika_id          = $kika_id;
        $company->name             = $request->session()->get('company_name');
        $company->email            = $request->session()->get('email');
        $company->website          = $request->session()->get('website');
        $company->contact_name     = $request->session()->get('contact_name');
        $company->contact_number   = $request->session()->get('contact_number');
        $company->city             = $request->session()->get('city');
        $company->state            = $request->session()->get('state');
        $company->country          = $request->session()->get('country');
        $company->type             = $request->session()->get('type');
        $company->kika_direct      = 0;
        $company->subscription_id  = $request->subscription;
        $company->referred_by      = $request->session()->get('is_referral_applied');
        $company->referral_code    = $request->session()->get('referral_code');
        $company->free_trial_day   = CompanyAdmin::getFreeTrialDay();
      }
      if($request->session()->get('is_referral_applied')) 
      {
          $refering_user = Companies::where('referral_code',$request->session()->get('referred_code'))->first();
          $company->referred_by = $refering_user->tbl_users_id ;
      }
      if ($user->save() && $company->save()) 
      {
        $agent                = new CompanyAgent();
        $agent->kika_id       = $company->kika_id;
        $agent->company_id    = $company->id;
        $agent->email         = $company->email;
        $agent->company_name  = $company->name;
        $agent->company_type  = $company->type;
        $agent->phone         = $company->contact_number;
        $agent->status        = 1;
        $agent->website       = $company->website;
        $agent->city          = $company->city;
        $agent->state         = $company->state;
        $agent->country       = $company->country;
        if($company->kika_direct == 1)
        {
          $agent->is_kika_direct = 1;
        }
        else
        {
          $agent->is_kika_direct = 0;
        }
        $agent->save();

        $subscription = Subscription::where('id',$request->subscription)->first();

        $request->session()->forget('company_name');
        $request->session()->forget('email');
        $request->session()->forget('password');
        $request->session()->forget('website');
        $request->session()->forget('referral_code');
        $request->session()->forget('is_referral_applied');
        $request->session()->forget('referred_code');
        $request->session()->forget('type');
        $request->session()->forget('country');
        $request->session()->forget('state');
        $request->session()->forget('city');
        $request->session()->forget('contact_name');
        $request->session()->forget('contact_number');
      
        if(Subscription::where('id',$request->subscription)->where('currency_id','=',null)->where('monthly_price','=',null)->exists())
        {
          $new_subscription = new UserSubscription();
          $new_subscription->subscription_id    = $request->subscription;
          $new_subscription->payment_id         = null;
          $new_subscription->user_id            = $user->id;
          $new_subscription->validity           = null;
          $new_subscription->currency_code      = null;
          $new_subscription->subscription_price = null;
          $new_subscription->addon_unit_price   = null;
          $new_subscription->addon_user         = null;
          $new_subscription->final_price        = null;
          $new_subscription->status             = 1;
          $new_subscription->save();
        }
        else if ($request->subscription !== null && $request->subscription !== '0') 
        {
          $request->session()->put('payment_for', 'new_plan');
          $request->session()->put('id', $user->id);
          $request->session()->put('amount', $subscription->monthly_price + $request->addon * $subscription->addon_price);
          $request->session()->put('currency', $subscription->currency ? $subscription->currency->currency_code : null );
          $request->session()->put('subscriptionID', $request->subscription);
          $request->session()->put('addon', $request->addon > 0 ? $request->addon : null);
          $request->session()->put('fetchPublishableUrl', 'profile/fetch-stripe-publishable');
          $request->session()->put('paymentUrl', 'profile/stripe/payment');
          $request->session()->put('redirectUrl', 'register/success');
          return redirect()->route('company-admin.stripe');
        }

        $data      = array('id'=>$user->id, 'kika_id' => $kika_id);
        $user_data = User::where('id',$user->id)->first();  
       
        Mail::send('mails.ActivationMail', $data, function($message) use($user_data) {
          $message->to($user_data->email)->subject('Welcome To Kika');
          $message->from('test.vpninfotech@gmail.com','Kika');
        });
        if(Mail::flushMacros())
        {
          session::flash('error','Mail not sent!!');
          return redirect('company-admin/register/step-1');
        }
        else
        {
          session::flash('success','Mail sent successfully!!');
          return view('auth.company-admin.register-step-final');
        }
      }
    }

   public function activeUser($id)
    {
      $id    = \Crypt::decrypt($id);
      $check = User::where('id',$id)->first();
      if(!is_null($check))
      {
        $user      = User::where('id',$id)->update(['status' => 1]);
        $user_data = User::where('status',1)->first();
        if($user_data)
        {
          return redirect('admin/login')->with('flash_message_success', 'You account is now active.');
        }
        else{
          echo "You are not eligible for login";
        }
      }
      else{
        echo "You are not verified user.";
      }
    }

    public function stripeForm()
    {
      // dd("here"); 
      // if (Session::has('payment_for') && Session::has('fetchPublishableUrl') && Session::has('paymentUrl') && Session::has('redirectUrl')) {
        return view('payment.stripe');
      // }else{
      //   return redirect()->route('/');
      // }
    }

    public function RegisterSuccess()
    {
      return view('auth.company-admin.register-step-final');
    }

    public function locationFetch(Request $request)
    {
      $select = $request->get('select');
      $value = $request->get('value');
      $dependent = $request->get('dependent');
      if ($dependent == 'state') {
          $data = States::where('country_id', $value)->get();
      }
      elseif($dependent == 'city'){
          $data = Cities::where('state_id', $value)->get();
      }
      $output = '<option value="0">Select '.ucfirst($dependent).'</option>';
      foreach($data as $row)
      {
          $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
      }
      echo $output;
    }

    public function getKikaID($type)
    {
      $company_month_count = Companies::where( DB::raw('DAY(created_at)'), '=', date('d') )->count();

      $kika_year  = date('y');
      $kika_month = date('m');
      $kika_day   = date('d');

      if ($type == 1) {
        $type_alpha = "A"; 
      }elseif ($type == 2) {
        $type_alpha = "B"; 
      }else{
        $type_alpha = "C"; 
      }
      $kika_count = sprintf('%03u', $company_month_count+1);
      $kika_id    = $kika_year.$kika_month.$kika_day.$type_alpha.$kika_count;

      return $kika_id;
    }

    function gererateReferralCode($company_name){

      $unique = false;

      do{
        $random = str_replace(' ', '', $company_name.'-'.Keygen::alphanum(6)->generate());

        $count = Companies::where('referral_code', '=', $random)->count();

        if( $count == 0){
            $unique = true;
        }
      }
      while(!$unique);

      return $random;
    }
}
