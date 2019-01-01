<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersSettings extends Model
{

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $fillable = ['user_id', 'weeks', 'lessons'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
