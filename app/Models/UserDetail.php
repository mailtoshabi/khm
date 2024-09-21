<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = ['user_id','contact_person','phone','address','logo'];

    public function user()
    {
        return $this->belongsTO('App\Models\User', 'user_id', 'id');
    }
}
