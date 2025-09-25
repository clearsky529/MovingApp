<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyAgent extends Model
{
	public function company()
    {
        return $this->hasOne('App\Companies','id','company_id');
    }

    public function companyType()
    {
        return $this->hasOne('App\CompanyType','id','company_type');
    }

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
}
