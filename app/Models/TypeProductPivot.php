<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TypeProductPivot extends Model
{
    protected $table = 'type_product_pivot';

    protected $fillable = ['type_id','product_id','mrp','stock'];

    public function prices()
    {
        return $this->hasMany('App\Models\PriceDetail','tp_pivot_id', 'id');
    }

}
