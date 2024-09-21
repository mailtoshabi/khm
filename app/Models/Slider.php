<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    const FILE_DIRECTORY = 'dir_sliders';
    protected $fillable = ['type','image','order_no'];
}
