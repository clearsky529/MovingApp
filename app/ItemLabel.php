<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemLabel extends Model
{
    // Added by JG VPN - 22-02-2024
    protected $fillable = ['item', 'parent_id', 'parent_item_id','item_type','is_master'];
}
