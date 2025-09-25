<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use App\Companies;
use App\CompanyUser;
use App\Constant;
use App\RiskAssessment;
use Illuminate\Support\Facades\Auth;
use Session;

class CompanyAdmin {
    /**
     * @param int $user_id User-id
     * 
     * @return string
     */
    public static function getCompanyId() 
    {
        $userId = '';
        if(Session::get('company-admin')){
            $userId = Session::get('company-admin');
        }
        elseif(Auth::user() != null){
            $userId = Auth::user()->id;
        }
        elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        {
            return redirect()->route('/');    
        }
    
        $company_id = Companies::where('tbl_users_id',$userId)->value('id');

        return json_encode($company_id);
    }

    public static function getCompanyUserCompany() 
    {
        $userId = '';
        if(Session::get('company-admin')){
            $userId = Session::get('company-admin');
        }
        elseif(Auth::user() != null){
            $userId = Auth::user()->id;
        }
        elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        {
            return redirect()->route('/');    
        }
      
        $company_id = CompanyUser::where('user_id',$userId)->value('company_id');

        return json_encode($company_id);
    }

    public static function getFreeTrialDay()
    {
        $days = Constant::where('name','referral_free_days')->value('value');

        return (int)$days;
    }

    public static function getDevicePrice()
    {
        $device_price = Constant::with('currency')->where('name','device_price')->first();

        return $device_price;
    }

    public static function getIcrPrice()
    {
        $icr_price = Constant::with('currency')->where('name','icr_price')->first();

        return $icr_price;
    }

    public static function getRiskAssessment($move_id,$move_type)
    {
        $risk_assessment_uplift = RiskAssessment::with('riskAssessmentDetail')->where([
                'move_id'       => $move_id,
                'move_type'     => $move_type
            ])->first();

        return $risk_assessment_uplift;
    }

}