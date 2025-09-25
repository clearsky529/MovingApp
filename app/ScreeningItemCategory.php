<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScreeningItemCategory extends Model
{
    public function Category()
    {
        return $this->hasOne('App\ScreeningCategories','id','category_id');
    }
}
