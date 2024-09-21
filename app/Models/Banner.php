<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    const FILE_DIRECTORY = 'dir_banners';
    protected $fillable = ['user_id','link','image','order_no'];

    public function affiliate()
    {
        return $this->belongsTO('App\Models\User', 'user_id', 'id');
    }
}
