<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
   

   	public function countryName()
    {
        return $this->hasOne('App\Countries','id','country');
    }

    public function stateName()
    {
        return $this->hasOne('App\States','id','state');
    }

    public function cityName()
    {
        return $this->hasOne('App\Cities','id','city');
    }

    public function companyType()
    {
        return $this->hasOne('App\CompanyType','id','type');
    }

    public function subscription()
    {
        return $this->hasOne('App\Subscription','id','subscription_id');
    }

    public function companySubscription()
    {
        return $this->hasOne('App\UserSubscription','user_id','tbl_users_id')->latest()->where('status',1);
    }

    public function user()
    {
        return $this->hasOne('App\User','id','tbl_users_id');
    }

    public function getRefferdCompany()
    {
        return $this->hasMany('App\Companies','referred_by','tbl_users_id');
    }
}
