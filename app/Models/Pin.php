<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $fillable = ['name'];

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store')->where('is_active',1)->withTimestamps();
    }

    public function stores_pg()
    {
        return $this->belongsToMany('App\Models\Store')->withTimestamps()->latest()->paginate(18);
    }
}
