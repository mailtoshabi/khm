<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $fillable = [
        'email', 'phone', 'password', 'status', 'is_active', 'is_access'
    ];
    protected $casts = ['is_active' => 'boolean','is_access' => 'boolean'];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function customer_detail() {
        return $this->hasOne('App\Models\Website\CustomerDetail','customer_id', 'id');
    }
}
