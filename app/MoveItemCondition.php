<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveItemCondition extends Model
{
    // Added by JG VPN - 22-02-2024
    protected $fillable = ['move_id', 'move_item_id', 'condition_id','move_type'];

    public function conditionDetails()
    {
        return $this->hasOne('App\CartonCondition','id','condition_id');
    }

    public function conditionSides()
    {
        return $this->hasMany('App\MoveItemConditionSide','item_condition_id');
    }

    public function conditionImage()
    {
        return $this->hasMany('App\MoveConditionImage','move_condition_id');
    }
}
