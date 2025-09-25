<?php
namespace App\Helpers;
use Illuminate\Support\Facades\App;
use App\CompanyUser;
use App\User;
use App\UserSubscription;
use App\Compamies;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Subscription;

class GetPlanDetails
{
  /**
   * @param int $user_id User-id
   * 
   * @return string
   */
  public static function getPlanDetail()
  {
    $userId = '';
    if (Session::get('company-admin')) {
      $userId = Session::get('company-admin');
    } elseif (Auth::user() != null) {
      $userId = Auth::user()->id;
    } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
      return redirect()->route('/');
    }

    $get_plan_details = User::with('getActiveSubscription', 'company')->where('id', $userId)->first();
    // dd($get_plan_details);
    if ($get_plan_details->role_id == 2) {
      if ($get_plan_details->company->subscription_id == NULL) {
        $response['status'] = 'free';
      } else {
        $validity = Subscription::where('title', '=', 'Enterprise')
          ->where('currency_id', '=', null)
          ->where('monthly_price', '=', null)
          ->where('monthly_max_moves', '=', null)
          ->first();
        // dd($get_plan_details->getActiveSubscription);
        // dd($validity);
        if ($get_plan_details->getActiveSubscription->validity != "") {
          $todayDate = date("Y-m-d");
          $lastdate = $get_plan_details->getActiveSubscription->validity;
          $datesum = date('Y-m-d', strtotime($lastdate . ' + ' . '1' . ' days'));
          if ($todayDate >= $datesum) {
            $response['status'] = 'expired';
          } elseif ($todayDate < $datesum) {
            $response['status'] = 'active';
          }
        } else {
          $todayDate = date("Y-m-d");
          $lastdate = $get_plan_details->getActiveSubscription->validity;
          // dd($lastdate);
          $datesum = date('Y-m-d', strtotime($lastdate . ' + ' . '1' . ' days'));
          // dd($datesum);
          if ($todayDate >= $datesum) {
            $response['status'] = 'expired';
          } elseif ($todayDate < $datesum) {
            $response['status'] = 'active';
          }
        }

      }
    } else {
      $response['status'] = false;
    }
    // dd($response['status']);
    return $response;
  }
}