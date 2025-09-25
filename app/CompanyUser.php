<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyUser extends Model
{

	use SoftDeletes;

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->hasOne('App\Companies','id','company_id');
    }

    public function userInfo()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
