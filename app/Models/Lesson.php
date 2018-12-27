<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    public $fillable = array('user_id','date','time');
    //
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
