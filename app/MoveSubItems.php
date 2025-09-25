<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveSubItems extends Model
{
    public function subItemDetails()
    {
        return $this->hasOne('App\ItemLabel','id','sub_item_id');
    }

    public function cartoonItemDetails()
    {
        return $this->hasOne('App\ItemLabel','id','sub_item_id');
    }
}
