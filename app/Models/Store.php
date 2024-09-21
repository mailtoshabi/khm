<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Store extends Model
{
    const FILE_DIRECTORY = 'dir_store';
    const FILE_DIRECTORY_BROCHURE = 'dir_store_brochures';
    protected $fillable = ['name','image','short_description','description','footer_description','brochure','email','phone','location','city','district','site_title','site_keywords','site_description'];

    public function pins()
    {
        return $this->belongsToMany('App\Models\Pin')->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category')->withTimestamps();
    }

}
