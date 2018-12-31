<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersSettings extends Model
{
    protected $fillable = [
        'user_id',
    ];

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
