<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiskAssessment extends Model
{

    protected $guarded = [];

    public function riskAssessmentDetail()
    {
        return $this->hasMany(RiskAssessmentDetail::class)->orderBy('id', 'asc');
    }
}
