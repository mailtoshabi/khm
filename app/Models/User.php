<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Zizaco\Entrust\Traits\EntrustUserTrait;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    // use EntrustUserTrait;
    use LaravelEntrustUserTrait;

    const FILE_DIRECTORY = 'dir_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function user_detail() {
        return $this->hasOne('App\Models\UserDetail','user_id', 'id');
    }

    public function affiliate() {
        return $this->hasOne('App\Models\Affiliate','user_id', 'id');
    }

    public function clinic() {
        return $this->hasOne('App\Models\Clinic','user_id', 'id');
    }

    public function brand() {
        return $this->hasOne('App\Models\Brand','user_id', 'id');
    }

    public function brands()
    {
        return $this->belongsToMany('App\Models\Brand')->where('is_active',1)->withTimestamps();
    }
}
