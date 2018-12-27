<?php

namespace App\Models;

use UsersSettings;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    public function settings()
    {
        return $this->hasOne('App\Models\UsersSettings');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', /* 'email', 'password', */ 'identity',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        /* 'password', */ 'remember_token',
    ];

    public function getPermittedWeeks() {
        return 2;
    }

    public function lessons()
    {
        return $this->hasMany('App\Models]Lesson');
    }
}
