<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryMoves extends Model
{
    public function move()
    {
        return $this->belongsTo('App\Move','move_id');
    }

    public function contact()
    {
        return $this->hasOne('App\MoveContact','move_id','move_id');
    }

    public function destinationAgent()
    {
        return $this->hasOne('App\CompanyAgent','kika_id','delivery_agent_kika_id');
    }

    public function contractor()
    {
        return $this->hasOne('App\CompanyAgent','kika_id','sub_contactor_kika_id');
    }
}
