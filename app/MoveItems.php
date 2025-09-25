<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveItems extends Model
{
    public function itemDetails()
    {
        return $this->hasOne('App\ItemLabel', 'id', 'item_id');
    }

    public function itemUpliftCategory()
    {
        return $this->hasOne('App\CartonChoice', 'id', 'screening_category_id');
    }

    public function itemScreeningCategory()
    {
        return $this->hasOne('App\ScreeningItemCategory', 'move_item_id', 'id');
    }

    public function itemPacker()
    {
        return $this->hasOne('App\PackerCode', 'id', 'packer_id');
    }

    public function subItems()
    {
        return $this->hasOne('App\MoveSubItems', 'move_item_id');
    }

    public function condition()
    {
        return $this->hasMany('App\MoveItemCondition', 'move_item_id')->where('move_type', 1);
    }

    public function container()
    {
        return $this->hasOne('App\ContainerItem', 'move_item_id', 'id');
    }

    public function transloadCondition()
    {
        return $this->hasMany('App\MoveItemCondition', 'move_item_id')->where('move_type', 4);
    }

    public function deliveryCondition()
    {
        return $this->hasMany('App\MoveItemCondition', 'move_item_id')->where('move_type', 5);
    }

    public function screeningCategory()
    {
        return $this->hasOne('App\ScreeningCategories', 'id', 'screening_category_id');
    }

    public function cartoonItem()
    {
        return $this->hasMany('App\MoveSubItems', 'move_item_id');
    }

    public function roomChoice()
    {
        return $this->hasOne('App\RoomChoice', 'id', 'room_id');
    }

 
}
