<?php
namespace App\Http\Controllers\company_admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\Admin\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use App\Helpers\GetPlanDetails;
use Stripe;
use App\User;
use App\{Move, Constant};
use App\CompanyUser;
use App\Companies;
use App\Cities;
use App\States;
use App\Countries;
use App\Currencies;
use App\StripePayment;
use App\UserPlanAddon;
use App\UserSubscription;
use App\Subscription;
use App\ScreeningMoves;
use App\CompanyAgent;
use App\TransloadMoves;
use Session;
use Mail;
use Carbon\Carbon;
use App\Helpers\CompanyAdmin;
use Log;
use Aws\S3\S3Client;
class DashboardController extends Controller
{
  // function __construct(){
  //   dd(Session::get('company-admin'));
  //   if(Session::get('company-admin')){
  //     dd('here');
  //     $userId = Session::get('company-admin');
  //   }
  //   else{
  //     // dd(Auth::user());
  //     $userId = Auth::user()->id;
  //   }
  // }

  public function index(Request $request)
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
      // return redirect('admin/login')->with('flash_message_success', 'You account in now active.');
    }

    // Handle email search functionality
    $searchUserId = $userId; // Default to logged-in user
    $searchedCompany = null;

    if ($request->has('search_email') && !empty($request->search_email)) {
      // Validate email format
      $request->validate([
        'search_email' => 'required|email'
      ]);

      // Find user ID from User table where email matches
      $searchedUser = User::where('email', $request->search_email)->first();

      if ($searchedUser) {
        // Find company info from company_users table where user_id matches
        $companyUser = CompanyUser::where('user_id', $searchedUser->id)->first();

        if ($companyUser) {
          $searchedCompany = Companies::where('id', $companyUser->company_id)->first();
          if ($searchedCompany) {
            $searchUserId = $searchedUser->id;
          }
        }
      }

      // Store search info for view
      $this->searched_email = $request->search_email;
      $this->searched_user = $searchedUser;
      $this->searched_company = $searchedCompany;
    }

    $this->companies = Companies::where('tbl_users_id', $userId)->where('kika_direct', 1)->first();
    $this->plan_details = UserSubscription::where('user_id', $userId)->latest()->first();
    $enterprise_id = $this->plan_details ? $this->plan_details->subscription_id : '';
    $this->subscription = Subscription::select('title')->where('id', $enterprise_id)->latest()->first();

    $this->plan_expiry_notification = null;

    if ($this->subscription != "") {
      if ($this->subscription->title != "Enterprise" && $this->subscription->title != "Kika Direct") {
        if ($this->plan_details) {
          $now = time();
          $then = strtotime($this->plan_details->validity);
          $difference = $then - $now;
          $days = floor($difference / (60 * 60 * 24));
          if ($days < 7) {
            if ($days < 0) {
              $this->plan_expiry_notification = null;
              // $this->plan_expiry_notification = "Please upgrade your plan, Current plan has expired! <a href=".route("company-admin.extend-plan").">Click here</a> to update your plan.";
            } else {
              $this->plan_expiry_notification = null;
              // $this->plan_expiry_notification = "Please upgrade your plan, Current plan will expire soon!";
            }
          } else {
            $this->plan_expiry_notification = null;
          }
        } else {
          $this->plan_expiry_notification = null;
        }
      } else {
        $this->plan_expiry_notification = null;
      }
    }

    $id = Companies::where('tbl_users_id', $userId)->first();
    if ($id->subscription_id == null) {
      $todayDate = date("Y-m-d");
      $mydate = $id->created_at;


      $daystosum = Companies::where('tbl_users_id', $userId)->value('free_trial_day');

      $datesum = date('Y-m-d', strtotime($mydate . ' + ' . $daystosum . ' days'));
      if ($todayDate >= $datesum) {
        $this->plan_expiry_notification = "Your free trial has ended. To continue access please update you plan <a href=" . route("company-admin.extend-plan") . ">here</a>";
      } else {
        $this->plan_expiry_notification = null;
      }
    }

    $company_id = Companies::where('tbl_users_id', $userId)->value('id');

    // Use searched user's company if email search is active
    $filterUserId = $searchUserId;
    $filterCompanyId = $searchedCompany ? $searchedCompany->id : $company_id;

    $this->user_job = Move::join('companies', 'companies.id', '=', 'company_id')
      ->where('archive_status', 0)
      ->where('tbl_users_id', '=', $filterUserId)
      ->count();

    $this->active_user = CompanyUser::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->whereHas('userInfo', function ($q) {
        $q->where('status', 1);
      })
      ->count();

    $this->delete_user = CompanyUser::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->where('deleted_at', '!=', 'null')
      ->whereMonth('deleted_at', Carbon::now()->month)
      ->withTrashed()
      ->count();

    $this->delete_move = Move::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->where('deleted_at', '!=', 'null')
      ->whereMonth('deleted_at', Carbon::now()->month)
      ->withTrashed()
      ->count();

    $jobCount['uplift'] = Move::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->where('archive_status', 0)
      ->whereHas('uplift')
      ->count();

    $jobCount['delivered'] = Move::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->where('archive_status', 0)
      ->whereHas('delivery')
      ->count();

    $jobCount['transload'] = Move::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->whereHas('transload', function ($q) {
        $q->where('status', 0);
      })
      ->where('archive_status', 0)
      ->count();

    $jobCount['screen'] = Move::whereHas('company', function ($q) use ($filterUserId) {
      $q->where('tbl_users_id', '=', $filterUserId);
    })
      ->whereHas('screening', function ($q) {
        $q->where('status', 0);
      })
      ->where('archive_status', 0)
      ->count();

    $this->jobCount = $jobCount;

    return view('theme.company-admin.index', $this->data);
  }

  public function changePasswordForm()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    return view('theme.company-admin.user.changepassword');
  }

  public function changePassword(Request $request)
  {
    app()->setLocale(session()->get("locale"));
    $request->validate(
      [
        'old_password' => ['required', 'min:8', 'max:20', new MatchOldPassword],
        'new_password' => ['required', 'min:8', 'max:20'],
        'confirm_password' => ['required', 'min:8', 'max:20', 'same:new_password'],
      ],
      [
        'old_password.required' => trans('auth.old_password.required'),
        'old_password.min' => 'The old password must be at least 8 characters.',
        'old_password.max' => trans('auth.old_password.max'),
        'new_password.required' => trans('auth.new_password.required'),
        'new_password.min' => 'The new password must be at least 8 characters.',
        'new_password.max' => trans('auth.new_password.max'),
        'confirm_password.required' => trans('auth.confirm_password.required'),
        'confirm_password.min' => 'The confirm password must be at least 8 characters.',
        'confirm_password.max' => trans('auth.confirm_password.max'),
      ]
    );

    $userId = '';
    // if(Session::get('company-admin')){
    //       $userId = Session::get('company-admin');
    // }
    // else{
    //   $userId = Auth::user()->id;
    // }
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }
    User::find($userId)->update(['password' => Hash::make($request->new_password)]);

    return redirect('company-admin/change-password')->with('flash_message_success', trans('Password changed successfully!'));
  }

  public function profile()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
      $this->user = User::where('id', $userId)->first();
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
      $this->user = Auth::user();
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    if ($user = UserSubscription::where('user_id', $userId)->latest()->first()) {
      $this->enterprise_plan = Subscription::where('title', '=', 'Enterprise')
        ->where('currency_id', '=', null)
        ->where('monthly_price', '=', null)
        ->where('monthly_max_moves', '=', null)
        ->first();
      if ($this->enterprise_plan != "") {
        $this->company = Companies::with('subscription', 'subscription.currency')
          ->where('tbl_users_id', $userId)
          ->first();
      }
    } else {
      $this->company = Companies::with('subscription', 'subscription.currency')
        ->where('tbl_users_id', $userId)
        ->first();
    }
    $this->get_plan_details = GetPlanDetails::getPlanDetail();

    // dd($this->get_plan_details);
    return view('theme.company-admin.user.profile', $this->data);
  }

  public function EditProfile(Request $request)
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    if ($request->isMethod('post')) {
      app()->setLocale(session()->get("locale"));
      $request->validate(
        [
          'company_name' => 'required|min:2',
          'email' => 'required|email|unique:users,email,' . $userId,
          'contact_name' => 'required|min:2|max:20',
          'website' => 'required|url',
          'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
          'icr_image' => 'nullable|mimes:jpg,png,jpeg,gif,svg',
          'color_code' => 'required_if:icr_toggle,==,0|nullable',
        ],
        [
          'username.required' => trans('user.username.required'),
          'username.min' => trans('user.username.min'),
          'username.max' => trans('user.username.max'),
          'email.required' => trans('user.email.required'),
          'email.email' => trans('user.email.email'),
          'image.required' => trans('user.image.required'),
          'image.mimes' => trans('user.image.mimes'),
          'icr_image.required_if' => trans('user.icr_image.required'),
          'icr_image.mimes' => trans('user.icr_image.mimes'),
          'color_code.required_if' => trans('user.color_code.required'),
        ]
      );
      // dd(env('AWS_BUCKET'));
      $users = User::findOrFail($userId);
      $users->email = $request->email;
      if ($request->hasFile('image')) {
        $filename = User::where('id', $userId)->value('profile_pic');
        // $exists = \Storage::has($filename);

        if (\Storage::has('/userprofile/' . $filename)) {
          \Storage::delete('/userprofile/' . $filename);
        }
        $image = $request->file('image');
        $name = time() . '.' . $image->getClientOriginalExtension();

        $file = $request->file('image');
        $imageName = time() . $file->getClientOriginalName();
        $filePath = '/userprofile/' . $imageName;
        \Storage::put($filePath, file_get_contents($request->file('image')));
        $users->profile_pic = $imageName;
      }
      $users->save();

      $company_id = Companies::where('tbl_users_id', $userId)->value('id');
      $company = Companies::findOrFail($company_id);

      $icr_title_image = null;
      $color_code = null;
      if ($request->icr_toggle == 0) {
        $icr_title_image = $company->icr_title_image;
        if ($request->hasFile('icr_image')) {

          if (\Storage::has('/icrtitle/' . $icr_title_image)) {
            \Storage::delete('/icrtitle/' . $icr_title_image);
          }

          $icr_image = $request->file('icr_image');
          $imageName = time() . $icr_image->getClientOriginalName();
          $filePath = '/icrtitle/' . $imageName;
          \Storage::put($filePath, file_get_contents($request->file('icr_image')));
          $icr_title_image = $imageName;
        }
        $color_code = $request->color_code;
      }
      $company->icr_title_image = $icr_title_image;
      $company->website = $request->website;
      $company->email = $request->email;
      $company->name = $request->company_name;

      $company->contact_name = $request->contact_name;
      $company->icr_title_toggle = $request->icr_toggle;
      $company->is_allow_icr_image = $request->is_allow_icr_image;
      $company->title_bar_color_code = $color_code;
      $company->save();

      $companyAgent = CompanyAgent::where('company_id', $company->id)->where('kika_id', $company->kika_id)->first();
      $companyAgent->company_name = $company->name;
      $companyAgent->save();

      $company_agent = CompanyAgent::where('kika_id', $company->kika_id)->first();
      if (isset($company_agent)) {
        CompanyAgent::where('kika_id', $company_agent->kika_id)
          ->update(['email' => $request->email, 'company_name' => $request->company_name]);
      }

      return redirect('company-admin/home')->with('flash_message_success', trans('common.User profile updated successfully!'));
    }
  }

  public function addOnUSer(Request $request)
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
      $user = User::where('id', $userId)->first();
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
      $user = Auth::user();
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $request->session()->put('id', $userId);
    $request->session()->put('amount', $user->company->subscription->addon_price * $request->add_on_user);
    $request->session()->put('currency', $request->currency);
    $request->session()->put('subscriptionID', $user->company->subscription_id);
    $request->session()->put('addon', $request->add_on_user);
    $request->session()->put('payment_for', 'addon');
    $request->session()->put('fetchPublishableUrl', 'profile/fetch-stripe-publishable');
    $request->session()->put('paymentUrl', 'profile/stripe/payment');
    $request->session()->put('redirectUrl', './profile');

    return redirect()->route('company-admin.stripe');
  }

  public function extendPlan()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
      $user = User::where('id', $userId)->first();
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
      $user = Auth::user();
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }
    $type_id = $user->company->type;
    // dd($type_id);
    $this->subscriptions = Subscription::with('currency')
      ->where('company_type', $type_id)
      ->where('status', 1)
      ->get();

    return view('theme.company-admin.user.subscription.extend', $this->data);
  }

  public function planPaymentGateway(Request $request)
  {
    // dd($request->all());
    $this->validate($request, [
      'subscription' => 'required',
      'add_on_users' => 'nullable|sometimes|gte:0',
    ]);

    $user = '';
    if (Session::get('company-admin')) {
      $user = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $user = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $subscription = Subscription::findOrFail($request->subscription);
    if ($request->add_on_users && $request->add_on_users > 0) {
      $final_price = $subscription->monthly_price + ($subscription->addon_price * $request->add_on_users);
    } else {
      $final_price = $subscription->monthly_price;
    }
    $company_id = CompanyAdmin::getCompanyId();

    if (Subscription::where('id', $request->subscription)->where('company_type', 2)->where('currency_id', '=', null)->where('monthly_price', '=', null)->exists()) {
      $last_subscription = UserSubscription::where('user_id', $user)->latest()->value('subscription_id');
      $last_sub_name = Subscription::where('id', $last_subscription)->value('title');
      $sub = Subscription::where('id', $request->subscription)->value('title');
      if ($last_sub_name == "Enterprise" || $last_sub_name == "Kika Direct") {
        $existing_company_device = CompanyUser::where('company_id', $company_id)->get();
        foreach ($existing_company_device as $device) {
          $deleteUser = User::where('id', $device->user_id)->delete();
        }
        $deleteCompanyUser = CompanyUser::where('company_id', $company_id)->delete();
      }

      UserSubscription::where('user_id', $user)->where('subscription_id', $last_subscription)->update(['status' => 0]);

      $new_subscription = new UserSubscription();
      $new_subscription->subscription_id = $request->subscription;
      $new_subscription->payment_id = null;
      $new_subscription->user_id = $user;
      $new_subscription->validity = null;
      $new_subscription->currency_code = null;
      $new_subscription->subscription_price = null;
      $new_subscription->addon_unit_price = null;
      $new_subscription->addon_user = null;
      $new_subscription->final_price = null;
      $new_subscription->status = 1;
      $new_subscription->save();

      $subcription_get = Subscription::where('id', $request->subscription)->first();
      if ($subcription_get->title == "Kika Direct") {
        $company_update = Companies::where('tbl_users_id', $new_subscription->user_id)
          ->update([
            'subscription_id' => $request->subscription,
            'kika_direct' => 1
          ]);
        $kika_direct_company = Companies::where('id', $company_id)->first();

        $update_agent = CompanyAgent::where('kika_id', $kika_direct_company->kika_id)->update(['is_kika_direct' => 1]);
        $user_update = User::where('id', $new_subscription->user_id)->update(['kika_direct' => 1]);
      } else {
        $company_update = Companies::where('tbl_users_id', $new_subscription->user_id)
          ->update([
            'subscription_id' => $request->subscription,
            'kika_direct' => 0
          ]);
        $kika_direct_company = Companies::where('id', $company_id)->first();
        $update_agent = CompanyAgent::where('kika_id', $kika_direct_company->kika_id)->update(['is_kika_direct' => 0]);
        $user_update = User::where('id', $new_subscription->user_id)->update(['kika_direct' => 0]);
      }
      return redirect('company-admin/profile')->with('flash_message_success', "Your subscription plan is successfully updated.");
    } elseif ($subtit = Subscription::where('id', $request->subscription)->exists()) {
      $tit = Subscription::where('id', $request->subscription)->first();
      if ($tit->title == "Kika Direct") {
        $request->session()->put('id', $user);
        $request->session()->put('amount', $final_price);
        $request->session()->put('currency', $subscription->currency->currency_code);
        $request->session()->put('subscriptionID', $request->subscription);
        $request->session()->put('addon', $request->add_on_users);
        $request->session()->put('payment_for', 'extend_plan');
        $request->session()->put('fetchPublishableUrl', 'profile/fetch-stripe-publishable');
        $request->session()->put('paymentUrl', 'profile/stripe/payment');
        $request->session()->put('redirectUrl', 'profile');
        return redirect()->route('company-admin.stripe');
      } else {
        $request->session()->put('id', $user);
        $request->session()->put('amount', $final_price);
        $request->session()->put('currency', $subscription->currency->currency_code);
        $request->session()->put('subscriptionID', $request->subscription);
        $request->session()->put('addon', $request->add_on_users);
        $request->session()->put('payment_for', 'extend_plan');
        $request->session()->put('fetchPublishableUrl', 'profile/fetch-stripe-publishable');
        $request->session()->put('paymentUrl', 'profile/stripe/payment');
        $request->session()->put('redirectUrl', 'profile');
        Log::info('stripe');
        return redirect()->route('company-admin.stripe');
      }
      //return redirect('company-admin/profile')->with('flash_message_success',"Your subscription plan is successfully updated.");
    } else {
      // dd('else');
      $last_subscription = UserSubscription::where('user_id', $user)->latest()->value('subscription_id');
      UserSubscription::where('user_id', $user)->where('subscription_id', $last_subscription)->update(['status' => 0]);

      $request->session()->put('id', $user);
      $request->session()->put('amount', $final_price);
      $request->session()->put('currency', $subscription->currency->currency_code);
      $request->session()->put('subscriptionID', $request->subscription);
      $request->session()->put('addon', $request->add_on_users);
      $request->session()->put('payment_for', 'extend_plan');
      $request->session()->put('fetchPublishableUrl', 'profile/fetch-stripe-publishable');
      $request->session()->put('paymentUrl', 'profile/stripe/payment');
      $request->session()->put('redirectUrl', 'profile');
      return redirect()->route('company-admin.stripe');
    }

  }

  public function referFriend(Request $request)
  {
    $validator = \Validator::make(
      $request->all(),
      [
        'email' => 'required|email|unique:users,email',
      ],
      [
        'email.unique' => 'Email is already registered with us.'
      ]
    );

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }

    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $email = $request->email;
    $data['refered_user'] = Companies::where('tbl_users_id', $userId)->first();

    Mail::send('mails.refer-friend', $data, function ($message) use ($email) {
      $message->to($email)->subject('Kika - Invitation');
    });

    \Session::flash('flash_message_success', 'Referral Mail Sent Successfully!');

  }

  public function selectedPlan(Request $request)
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $subscriptionPlan = Subscription::with('currency')->where('id', $request->plan)->value('title');
    if ($subscriptionPlan == 'Kika Direct') {
      $icr_price = Constant::with('currency')->where('name', '=', 'icr_price')->first();
      $subscriptionPlan = Subscription::with('currency')->where('id', $request->plan)->first();
      return array($icr_price, $subscriptionPlan);
    }
    return $subscriptionPlan;
  }
}
