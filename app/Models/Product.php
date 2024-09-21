<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Utilities\Utility;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    const FILE_DIRECTORY = 'image_products';
    const FILE_DIRECTORY_BROCHURE = 'image_brochures';
    protected $fillable = ['uuid','user_id','name','image','images','video','description','brochure','prod_stock','unit_om','hsn_code','tax','is_featured','is_home','delivery_unit','delivery_min','delivery_max','site_title','site_keywords','site_description'];
    protected $casts = [
        'is_featured' => 'boolean',
        'is_home' => 'boolean',
        'images' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category')->withTimestamps();
    }

    public function parent_cats() {
        $parentCats = [];
        foreach($this->categories as $category) {
            foreach($category->parents as $parent) {
                $parentCats[] = $parent;
            }

        }
        return $parentCats;
    }

    public function brands()
    {
        return $this->belongsToMany('App\Models\Brand')->withTimestamps();
    }

    public function brand()
    {
        $brand = $this->brands()->orderBy('id','desc')->first();
        if($brand) {
            $all_slug = AllSlug::where('causer_id', $brand->id)->where('causer_type', 'App\Models\Brand')->first();
            $brand->slug = $all_slug->slug;
            return $brand;
        }else {
            return '';
        }
    }

    public function affiliates()
    {
        return $this->belongsToMany('App\Models\Affiliate')->withTimestamps();
    }

    /*public function prices()
    {
        return $this->hasMany('App\Models\PriceDetail','product_id', 'id');
    }*/

    /*public function product_types()
    {
        return $this->hasMany('App\Models\PriceDetail','type_id', 'id');
    }*/

    public function product_types()
    {
        return $this->belongsToMany('App\Models\ProductType', 'type_product_pivot', 'product_id', 'type_id')->withTimestamps()->withPivot('mrp', 'stock');
        /*return $this->belongsToMany('App\Models\ProductType', 'type_product_pivot', 'product_id', 'type_id')->using('App\Models\TypeProductPivot');*/
    }

    public function min_size_detail()
    {
        $size = TypeProductPivot::where('product_id',$this->id)->orderBy('id','asc')->first();


        if($size) {
            $data = $size;
        }else {
            $data = '';
        }
        return $data;
    }

    public function min_price_detail()
    {
        if(empty($this->min_size_detail())) {
            $min_price = '';
        }else {
            $prices = PriceDetail::where('tp_pivot_id',$this->min_size_detail()->id)->get();
            if($prices) {
                $min_price = $prices->min();
            }
            else {
                $min_price = '';
            }
        }
        return $min_price;
    }

    public function max_price_detail()
    {
        if(empty($this->min_size_detail())) {
            $max_price = '';
        }else {
            $prices = PriceDetail::where('tp_pivot_id',$this->min_size_detail()->id)->get();
            if($prices) {
                $max_price = $prices->max();
            }
            else {
                $max_price = '';
            }
        }
        return $max_price;
    }

    public function min_size()
    {
        if(empty($this->min_size_detail())) {
            $data = '';
        }else {
            $data = $this->min_size_detail()->type_id;
        }
        return $data;
    }

    public function min_quantity()
    {
        /*$prices = PriceDetail::where('product_id',$this->id)->where('type_size',$this->min_size())->get(['quantity_from']);

        return $prices->min()->quantity_from;*/
        if(empty($this->min_price_detail())) {
            $data = '';
        }else {
            $data = $this->min_price_detail()->quantity_from;
        }
        return $data;
    }
    public function min_price()
    {
        /*$prices = PriceDetail::where('product_id',$this->id)->where('type_size',$this->min_size())->where('quantity_from',$this->min_quantity())->get(['price']);

        return $prices->min()->price;*/

        if(empty($this->min_price_detail())) {
            $min_price = '';
        }else {
            $min_price = $this->min_price_detail()->price;
        }
        return $min_price;
    }

    public function min_mrp()
    {
        if(empty($this->min_size_detail())) {
            $data = '';
        }else {
            $data = $this->min_size_detail()->mrp;
        }
        return $data;
    }

    public function max_quantity($type)
    {
        /*$prices = PriceDetail::where('product_id',$this->id)->where('type_size',$type)->get(['quantity_from']);

        return $prices->max()->quantity_from;*/
        if(empty($this->max_price_detail())) {
            $data = '';
        }else {
            $data = $this->max_price_detail()->quantity_from;
        }
        return $data;
    }

    public function max_size_detail()
    {
        $size = TypeProductPivot::where('product_id',$this->id)->orderBy('id','desc')->first();
        if($size) {
            $data = $size;
        }else {
            $data = '';
        }
        return $data;
    }


    public function max_price()
    {
        /*$prices = PriceDetail::where('product_id',$this->id)->where('type_size',$this->min_size())->where('quantity_from',$this->min_quantity())->get(['price']);

        return $prices->min()->price;*/

        if(empty($this->max_price_detail())) {
            $min_price = '';
        }else {
            $min_price = $this->max_price_detail()->price;
        }
        return $min_price;
    }

    public function getCostPrice() {

    }


    public function sgst()
    {
        return $this->tax/2;
    }

    public function cgst()
    {
        return $this->tax/2;
    }

    public function basicTypeMinPrice()
    {
        $types = TypeProductPivot::where('product_id',$this->id)->get();
        foreach($types as $type) {
            $type->min_price = $type->prices->min('price');
        }
        return $types->min('min_price');
    }

    public function basicProduct() {
        $types = TypeProductPivot::where('product_id',$this->id)->get();
        foreach($types as $type) {
            $type->min_price = $type->prices->min('price');
        }
        $min_price = $this->basicTypeMinPrice();
        $min_type = $types->where('min_price',$min_price)->first();
        $data = [];
        $data['type'] = $min_type->type_id;

        $max_price = $this->productTypePrice($min_type->type_id)['max'];
        $max_price = $max_price == 0 ? '' : $max_price;

        if(empty($min_type->mrp) || ($min_type->mrp == 0)) {
            $data['mrp'] = $max_price;
        }else {
            $data['mrp'] = round($min_type->mrp,2);
        }


        return  $data;
    }

    public function basicAffiliate() {
        $affiliate_id = session('kerala_h_m_affiliate');
        $affiliate_product_type = DB::table('affiliate_product_type')->where(['affiliate_id' => $affiliate_id, 'product_id' => $this->id, 'type_id' => $this->basicProduct()['type']])->first();
        /*$price = (isset($affiliate_product_type->profit) && !empty($affiliate_product_type->profit)) ? round(Utility::getAffiliatePrice($this->id,$this->basicProduct()['type'])['cost'] + (Utility::getAffiliatePrice($this->id,$this->basicProduct()['type'])['cost'] * ($affiliate_product_type->profit/100)),2) : round(Utility::getAffiliatePrice($this->id,$this->basicProduct()['type'])['khm'],2);*/
        if(isset($affiliate_product_type->profit) && !empty($affiliate_product_type->profit)) {
            $profit_type = $affiliate_product_type->profit_type;
            $basic_cost = Utility::getAffiliatePrice($this->id,$this->basicProduct()['type'])['cost'];
            if($profit_type == Utility::PROFIT_TYPE_PERCENTAGE) {
                $price =  round($basic_cost + ($basic_cost * ($affiliate_product_type->profit/100)),2);
            }else if($profit_type == Utility::PROFIT_TYPE_MARGIN) {
                $price =  round($basic_cost + $affiliate_product_type->profit,2);
            }else {
                $price =  round($affiliate_product_type->profit,2);
            }

        }else {
            $price =  round(Utility::getAffiliatePrice($this->id,$this->basicProduct()['type'])['khm'],2);
        }

        $data = [];
        if(!empty($this->basicProduct()['mrp']) && ($this->basicProduct()['mrp'] != 0)) {
            $disc_perc = round((($this->basicProduct()['mrp'] - $price)/$this->basicProduct()['mrp'])*100,0);
            $data['off'] = $disc_perc;
        }else {
            $data['off'] = '';
        }
        $data['price'] = round($price,2);
        return $data;
    }

    public function basicKhm() {
        $price = Utility::getAffiliatePrice($this->id,$this->basicProduct()['type'])['khm'];

        $data = [];
        if(!empty($this->basicProduct()['mrp'])) {
            $disc_perc = round((($this->basicProduct()['mrp'] - $price)/$this->basicProduct()['mrp'])*100,0);
            $data['off'] = $disc_perc;
        }else {
            $data['off'] = '';
        }
        $data['price'] = round($price,2);
        return $data;
    }

    public function productTypePrice($type)
    {
        $types = TypeProductPivot::where('type_id',$type)->where('product_id',$this->id)->first();
        $price = [];
        $price['min'] = $types->prices->min('price');
        $price['max'] = $types->prices->max('price');
        return $price;
    }

    public function getYoutubeCodeAttribute() {
        if (str_contains($this->video, 'watch?v=')) {
            $text = explode('watch?v=', $this->video, 2)[1];
        }
        elseif (str_contains($this->video, 'shorts/')) {
            $text = explode('shorts/', $this->video, 2)[1];
        }
        elseif(str_contains($this->video, 'youtu.be/'))
        {
            $text = explode('youtu.be/', $this->video, 2)[1];
        }
        else {
            $text = 'Unsupported Video link format';
        }
        return $text;
    }

}
