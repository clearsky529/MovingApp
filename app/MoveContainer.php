<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveContainer extends Model
{
     protected $table = 'move_containers';

    public function containerItems()
    {
        return $this->hasMany('App\ContainerItem','container_id');
    }

    public function Category()
    {
        return $this->hasOne('App\ScreeningCategories','id','category_id');
    }

}
