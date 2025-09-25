<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveItemConditionSide extends Model
{
    public function sideDetails()
    {
        return $this->hasOne('App\ConditionSide','id','condition_side_id');
    }
}
