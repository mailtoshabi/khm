<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Brand extends Model
{
    const FILE_DIRECTORY = 'dir_brands';
    const SLIDER_DIRECTORY = 'dir_slider';
    protected $fillable = ['user_id','name','image','images','site_title','site_keywords','site_description'];

    protected $casts = [
        'images' => 'array',
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withTimestamps();
    }

    public function affiliates()
    {
        return $this->belongsToMany('App\Models\Affiliate')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTO('App\Models\User', 'user_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User')->withTimestamps();
    }

    public function dealers()
    {
        return $this->belongsToMany('App\Models\User')->wherePivot('user_id', '!=', Auth::id())->withTimestamps();
    }

}
