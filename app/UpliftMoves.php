<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpliftMoves extends Model
{
    public function move()
    {
        return $this->belongsTo('App\Move','move_id');
    }

    public function contact()
    {
        return $this->hasOne('App\MoveContact','move_id','move_id');
    }

    public function originAgent()
    {
        return $this->hasOne('App\CompanyAgent','kika_id','origin_agent_kika_id');
    }
}
