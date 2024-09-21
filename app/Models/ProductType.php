<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'type_product_pivot', 'type_id', 'product_id')->withTimestamps()->withPivot('mrp', 'stock');
        /*return $this->belongsToMany('App\Models\Product', 'type_product_pivot', 'type_id', 'product_id')->using('App\Models\TypeProductPivot');*/
    }

}
