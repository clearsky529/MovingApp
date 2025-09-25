<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransloadMoves extends Model
{
    public function move()
    {
        return $this->belongsTo('App\Move','move_id');
    }

    public function activity()
    {
        return $this->hasMany('App\TransloadActivity','transload_id','id');
    }

    public function contact()
    {
        return $this->hasOne('App\MoveContact','move_id','move_id');
    }
}
