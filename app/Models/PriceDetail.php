<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceDetail extends Model
{
    protected $fillable = ['tp_pivot_id','quantity_from','quantity_to','price'];

    public function type()
    {
        return $this->belongsTO('App\Models\TypeProductPivot', 'tp_pivot_id','id');
    }
}
