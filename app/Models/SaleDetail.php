<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $fillable = ['sale_id','product_id','quantity','price'];

    public function sale()
    {
        return $this->belongsTO('App\Models\Sale', 'sale_id', 'id');
    }

    public function product()
    {
        return $this->belongsTO('App\Models\Product', 'product_id', 'id');
    }

}
