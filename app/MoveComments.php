<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveComments extends Model
{
    public function image()
    {
        return $this->hasMany('App\CommentImages','comment_id');
    }
}
