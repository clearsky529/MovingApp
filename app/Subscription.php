<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function companyType()
    {
        return $this->hasOne('App\CompanyType','id','company_type');
    }

    public function currency()
    {
        return $this->hasOne('App\Currencies','id','currency_id');
    }

}
