<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContainerItem extends Model
{
     protected $table = 'container_items';

     public function itemDetails()
     {
        return $this->hasOne('App\MoveItems','id','move_item_id');
     }

     public function containerDetails()
     {
     	return $this->hasOne('App\MoveContainer','id','container_id');
     }
}
