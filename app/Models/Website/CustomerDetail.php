<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $fillable = ['customer_id','name','address','profile_pic','gstin'];
    protected $casts = ['address' => 'array'];

    public function customer()
    {
        return $this->belongsTO('App\Models\Website\Customer', 'customer_id', 'id');
    }
}
