<?php

namespace App\Http\Controllers\company_admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Companies;
use App\Move;
use App\CompanyUser;
use App\Cities;
use App\OauthAccessToken;
use App\States;
use App\Countries;
use App\UserSubscription;
use App\Subscription;
use App\Mail\DeactivateUser;
use App\Helpers\CompanyAdmin;
use App\Helpers\UserSubscriptionPlan;
use App\Helpers\GetPlanDetails;
use DB;
use Carbon\Carbon;
use Session;
use Crypt;
use Illuminate\Validation\Rule;
class CompanyUserController extends Controller
{
  public function index()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $company_id = Companies::where('tbl_users_id', $userId)->value('id');

    $this->company_user = CompanyUser::where('company_id', $company_id)->get();

    return view('theme.company-admin.company-user.index', $this->data);
  }

  public function show($id)
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }
    $id = \Crypt::decrypt($id);

    $company_id = Companies::where('tbl_users_id', $userId)->value('id');

    $this->users = CompanyUser::where('company_id', $company_id)->where('id', $id)->first();

    return view('theme.company-admin.company-user.view', $this->data);
  }

  public function create()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $company_id = CompanyAdmin::getCompanyId();
    $user_count = CompanyUser::where('company_id', $company_id)->count();

    $user_plan = UserSubscription::with('subscription')
      ->where('user_id', $userId)
      ->where('status', 1)
      ->first();

    // var_dump($user_plan);

    $id = Companies::where('tbl_users_id', $userId)->first();

    if ($id->subscription_id != null) {
      $get_plan_details = GetPlanDetails::getPlanDetail();

      if ($get_plan_details['status'] == 'expired') {
        return redirect('company-admin/device')->with('flash_message_error', 'Your plan is expired!');
      } elseif ($get_plan_details['status'] == 'active') {
        $max_user = $user_plan->addon_user + $user_plan->subscription->free_users;
        if ($user_plan->subscription->free_users != "") {
          if ($max_user <= $user_count) {
            return redirect('company-admin/device')->with('flash_message_error', 'User limit reached, Please upgrade plan to register more user!');
          } else {
            return view('theme.company-admin.company-user.create');
          }
        } else {
          return view('theme.company-admin.company-user.create');
        }
      }
    } elseif ($id->subscription_id == null) {
      $todayDate = date("Y-m-d");
      $mydate = $id->created_at;
      $daystosum = '30';
      $datesum = date('Y-m-d', strtotime($mydate . ' + ' . $daystosum . ' days'));
      if ($todayDate >= $datesum) {
        return redirect('company-admin/device')->with('flash_message_error', 'Your free user plan have been expired to continue with our service please extend your plan.');
      } else {
        return view('theme.company-admin.company-user.create');
      }
    }
  }

  public function store(Request $request)
  {
    $user = User::where('username', $request->devicename)->where('deleted_at', '=', null)->first();
    if (!empty($user)) {
      $this->validate(
        $request,
        [
          'devicename' => 'required|string|min:2|regex:/^\S*$/u|unique:users,username',
          'password' => 'required|min:8',
          'confirm-password' => 'required|min:8|same:password',
          // 'phone'             => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
        ],
        [
          // 'phone.required' => 'The contact number field is required.' ,
          // 'phone.regex' => 'The contact number is not valid.' ,
          'devicename.regex' => 'The devicename field must not have space.',
          // 'phone.min' => 'The contact number must be at least 6.' ,
        ]
      );
    } else {
      $this->validate(
        $request,
        [
          'devicename' => 'required|string|min:2|regex:/^\S*$/u',
          'password' => 'required|min:8',
          'confirm-password' => 'required|min:8|same:password',
          // 'phone'             => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
        ],
        [
          // 'phone.required' => 'The contact number field is required.' ,
          // 'phone.regex' => 'The contact number is not valid.' ,
          'devicename.regex' => 'The devicename field must not have space.',
          // 'phone.min' => 'The contact number must be at least 6.' ,
        ]
      );
    }

    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $userTypeId = Session::get('userTypeId');
    $companyId = Companies::where('tbl_users_id', $userId)->value('id');
    $companyEmail = Companies::where('tbl_users_id', $userId)->value('email');
    $company_user = Companies::where('id', $companyId)->first();

    $user = new User();
    $user->username = $request->devicename;
    $user->password = bcrypt($request->password);
    $user->email = $companyEmail;
    if ($company_user->kika_direct == 1) {
      $user->kika_direct = 1;
    }
    $user->role_id = 3;
    $user->save();
    $user->assignRole('company-user');

    $companyUser = new CompanyUser();
    $companyUser->company_id = $companyId;
    $companyUser->user_id = $user->id;
    // $companyUser->name        = $request->username;
    $companyUser->created_by = $userTypeId ? $userTypeId : null;
    // $companyUser->phone       = $request->phone;

    $companyUser->save();

    return redirect('company-admin/device')->with('flash_message_success', 'Device added successfully!');
  }

  public function edit($id)
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $id = \Crypt::decrypt($id);

    $company_id = Companies::where('tbl_users_id', $userId)->value('id');

    $this->user = CompanyUser::where('company_id', $company_id)->where('id', $id)->first();

    return view('theme.company-admin.company-user.edit', $this->data);
  }

  public function update(Request $request, $id)
  {
    // dd($request->all());
    $companyUser = CompanyUser::where('id', $id)->where('deleted_at', '=', null)->first();
    $this->validate(
      $request,
      [
        'devicename' => ['required', Rule::unique('users', 'username')->ignore($companyUser->user_id)->whereNull('deleted_at')]
      ],
      [
        'devicename.regex' => 'The devicename field must not have space.'
      ]
    );

    if ($request->password && $request->confirm_password) {
      $this->validate($request, [
        'password' => 'required_with:confirm_password|nullable|min:8|same:confirm_password',
        'confirm-password' => 'nullable|same:password|min:8',
      ]);
    } elseif ($request->password) {
      $this->validate($request, [
        'confirm-password' => 'required|same:password|min:8',
      ]);
    } elseif ($request->confirm_password) {
      $this->validate($request, [
        'password' => 'required|same:password|min:8',
      ]);
    }

    if (!$request->device_email) {
      $this->validate(
        $request,
        [
          'device-email' => 'required',
        ],
      );
    }

    $user_id = CompanyUser::where('id', $id)->value('user_id');
    $company_email = Companies::where('tbl_users_id', $user_id)->value('email');
    $user = User::where('id', $user_id)->first();

    $user->username = $request->devicename;
    if ($request->password) {
      $user->password = bcrypt($request->password);
    }
    if ($request->device_email) {
      $user->email = $request->device_email;
    }
    $user->save();

    // $companyDetails = Companies::where('id', $company_id)->first();
    // $companyDetails->email = $request->company_email;
    // $companyDetails->save();

    return redirect('company-admin/device')->with('flash_message_success', 'Device updated successfully!');

  }

  public function delete($id)
  {
    // $id = \Crypt::decrypt($id);
    $user_id = CompanyUser::where('id', $id)->value('user_id');
    User::where('id', $user_id)->delete();
    CompanyUser::where('id', $id)->delete();

    return redirect('company-admin/device')->with('flash_message_success', 'Device deleted successfully!');
  }

  public function changeStatus(Request $request)
  {
    $user = CompanyUser::with('userInfo')->where('id', $request->user_id)->first();
    $user->userInfo->status = $request->status;
    $user->push();
  }

  public function logoutUser($id)
  {
    $user = CompanyUser::where('id', $id)->first();
    $user->is_login = 0;
    $user->push();
    OauthAccessToken::where('user_id', $user->user_id)->delete();
  }

  public function deleteUserDetails()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }
    $companyId = Companies::where('tbl_users_id', $userId)->value('id');
    $this->user = CompanyUser::join('users', 'users.id', '=', 'company_users.user_id')
      ->where('company_users.company_id', $companyId)
      ->where('company_users.deleted_at', '!=', 'null')
      ->whereMonth('company_users.deleted_at', Carbon::now()->month)
      ->withTrashed()
      ->get();
    return view('theme.company-admin.company-user.userDetails', $this->data);
  }

}