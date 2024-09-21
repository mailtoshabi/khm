<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Affiliate extends Model
{
    const FILE_DIRECTORY = 'dir_affiliates';
    protected $fillable = [/*'user_name','password','name',*/'user_id','image','description','location','city','district','pin','contact_email','contact_phone','contact_whatsapp','footer_description','site_title','site_keywords','site_description','upi_id','g_pay','bank_account'];

    protected $casts = [
        'image' => 'array',
    ];

    /*public function treatments()
    {
        return $this->belongsToMany('App\Models\Treatment')->withTimestamps();
    }*/

    public function user()
    {
        return $this->belongsTO('App\Models\User', 'user_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withTimestamps();
    }

    public function offer_products()
    {
        return $this->belongsToMany('App\Models\Product')->where('is_active', 1)->wherePivot('is_offer', 1)->latest();
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category')->withTimestamps();
    }

    public function main_categories()
    {
        return $this->belongsToMany('App\Models\Category')->where('is_active',1)->orderBy('order_no','asc'); //->has('parents',0)
    }

    public function child_categories()
    {
        return $this->belongsToMany('App\Models\Category')->has('parents','!=',0)->orderBy('order_no','asc');
    }

    public function brands()
    {
        return $this->belongsToMany('App\Models\Brand')->where('is_active',1)->withTimestamps();
    }

    public function banners()
    {
        return $this->belongsToMany('App\Models\Banner')->withTimestamps();
    }

}
