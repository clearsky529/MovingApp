<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Move extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function type()
    {
        return $this->hasOne('App\MoveType', 'id', 'type_id');
    }

    public function company()
    {
        return $this->hasOne('App\Companies', 'id', 'company_id');
    }

    public function uplift()
    {
        return $this->hasOne('App\UpliftMoves', 'move_id');
    }

    public function transit()
    {
        return $this->hasOne('App\TransitMoves', 'move_id');
    }

    public function delivery()
    {
        return $this->hasOne('App\DeliveryMoves', 'move_id');
    }

    public function screening()
    {
        return $this->hasOne('App\ScreeningMoves', 'move_id');
    }

    public function transload()
    {
        return $this->hasOne('App\TransloadMoves', 'move_id');
    }

    public function contact()
    {
        return $this->hasOne('App\MoveContact', 'move_id');
    }

    public function controllingAgent()
    {
        return $this->hasOne('App\CompanyAgent', 'id', 'controlling_agent');
    }

    public function destinationAgent()
    {
        return $this->hasOne('App\CompanyAgent', 'id', 'destination_agent');
    }

    public function items()
    {
        return $this->hasMany('App\MoveItems', 'move_id')->orderBy('item_number');
    }

    public function transloadContainer()
    {
        return $this->hasMany('App\MoveContainer', 'move_id');
    }

    public function transhipPostPackageSignature()
    {
        return $this->hasOne('App\PackageSignature', 'move_id', 'id')->latest()
            ->where('status', 1)
            ->where('move_type', '=', '4');
    }

    public function transhipPrePackageSignature()
    {
        return $this->hasOne('App\PackageSignature', 'move_id', 'id')->latest()
            ->where('status', 0)
            ->where('move_type', '=', '4');
    }

    public function upliftRiskAssessment()
    {
        return $this->hasOne('App\RiskAssessment', 'move_id')->latest()->where('move_type', 1);
    }

    public function deliveryRiskAssessment()
    {
        return $this->hasOne('App\RiskAssessment', 'move_id')->latest()->where('move_type', 5);
    }

    public function roomChoice()
    {
        return $this->hasOne('App\RoomChoice', 'id', 'room_id');
    }
}
