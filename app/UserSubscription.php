<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    public function subscription()
    {
        return $this->hasOne('App\Subscription','id','subscription_id');
    }

    public function user()
    {
    	return $this->hasOne('App\User','user_id','id');
    }
}
