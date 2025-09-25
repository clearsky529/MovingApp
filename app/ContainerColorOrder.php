<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContainerColorOrder extends Model
{
    public function next(){

        return ContainerColorOrder::where('id', '>', $this->id)->orderBy('id','asc')->first();
    
    }
}
