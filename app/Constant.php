<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Constant extends Model
{
    public $timestamps = false;

      public function currency()
    {
        return $this->hasOne('App\Currencies','id','currency_id');
    }
}
