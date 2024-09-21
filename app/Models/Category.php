<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const FILE_DIRECTORY = 'dir_categories';
    protected $fillable = ['name','image','order_no','site_title','site_keywords','site_description']; //'parent',
    protected $casts = ['parent' => 'boolean'];
    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->where('is_active',1)->withTimestamps();
    }

    /*public function childs() {
        return $this->hasMany('App\Models\Category','parent','id')->orderBy('name','asc') ;
    }*/

    public function parents() {
        return $this->belongsToMany('App\Models\Category', 'category_parent', 'category_id', 'parent_id')->where('is_active',1);
    }

    public function childs() {
        return $this->belongsToMany('App\Models\Category', 'category_parent', 'parent_id', 'category_id')->orderBy('order_no','asc')->where('is_active',1);
    }

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store')->withTimestamps();
    }

    /*public function parent()
    {
        return $this->belongsTo('App\Models\Category', 'parent');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Category', 'parent');
    }*/

    public function parent_name() {

        if($this->parent == 0) {
            $parent_name = '';
        }else {
            $parent = Category::where('id',$this->parent)->first();
            $parent_name = $parent->name;
        }
        return $parent_name;
    }

    public function have_parent() {
        $childs = Category::where('parent',$this->id)->get();
        if($childs->count()==0) { return false; }
        else { return true; }
    }

    public function child_categories() {
        $childs = Category::where('parent',$this->id)->get();
        return $childs;
    }

    public function affiliates()
    {
        return $this->belongsToMany('App\Models\Affiliate')->withTimestamps();
    }
}
