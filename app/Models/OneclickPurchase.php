<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OneclickPurchase extends Model
{
    protected $fillable = ['phone','user_id','product_id','is_active'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function user()
    {
        return $this->belongsTO('App\Models\User', 'user_id', 'id');
    }
}
