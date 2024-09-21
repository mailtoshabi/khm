<?php

namespace App\Http\Utilities;
use App\Models\AllSlug;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\TypeProductPivot;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Utility{

    //Constants

    const STORAGE_DEFAULT = '';

    const ADMIN_ID = 1;

    const ADMIN_ROLE_ID = 1;
    const AFFILIATE_ROLE_ID = 2;
    const CLINIC_ROLE_ID = 3;
    const BRAND_ROLE_ID = 4;

    const CMS_USER_ID = 1;
    const KHM_USER_ID = 2;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const USER_TYPE_ADMIN = 'admin';
    const USER_TYPE_LAB = 'employee';

    const THEME_ADMIN = 'admin/';
    const DEFAULT_STORAGE = 'storage/';

    const CURRENCY_CODE = 'INR';
    const schemecode = 'FIRST';
    const marchantId = 'L1035034'; //T206030
    const FEDSALT = '1577188999OIJKAH'; //3976262521OAOQBJ

    const CATEGORY_ID_OFFER = 11;

    const STATE_ID_KERALA = 18;

    const PAYMENT_ONLINE = 1;
    const PAYMENT_OFFLINE = 2;
    const PAYMENT_COD = 6;

    const SALE_STATUS_NEW = 1;
    const SALE_STATUS_CANCELLED = 6;
    const SALE_STATUS_CLOSED = 7;

    const SALE_PAID = 1;
    const SALE_NOTPAID = 0;

    const PROFIT_TYPE_PERCENTAGE = 1;
    const PROFIT_TYPE_MARGIN = 2;
    const PROFIT_TYPE_SELLINGRATE = 3;

    const DELIVERY_TYPE_PAID = 0;
    const DELIVERY_TYPE_TOPAY = 1;

    const SALE_STATUS_CANCELLED_BY_CUST = 1;

    const DEFAULT_CUSTOMER_EMAIL = 'nothing@keralahealthmart.com';
    const DEFAULT_CUSTOMER_PHONE = '0000000000';

    const DEFAULT_DB_ORDER = '999123999';
    const TEMP_PWD = 'khM_KERALhealthmart_2018*&^%)(cms';
    const KHM_BIG_IMAGE_SIZE = '423x460';

    const DEFAULT_IMAGE_W = '200';
    const DEFAULT_IMAGE_H = '200';

    const IMAGE_TREATMENT_ORIGINAL= '612x220';
    const IMAGE_TREATMENT_THUMB = '170x65';

    const IMAGE_CLINIC_ORIGINAL= '565x557';
    const IMAGE_CLINIC_THUMB = '192x188';

    const IMAGE_DOCTOR_ORIGINAL= '150x150';
    const IMAGE_DOCTOR_THUMB = '100x100';

    const IMAGE_AFFILIATE_ORIGINAL= '296x55';
    const IMAGE_AFFILIATE_THUMB = '148x28';

    const IMAGES_DOCTOR_ORIGINAL= '612x360';
    const IMAGES_DOCTOR_THUMB = '170x65';

    const IMAGE_BRAND = '200x90';
    const IMAGE_CATEGORY = '206x224';

    const IMAGE_PRODUCT_THUMB = '206x224';
    const IMAGE_PRODUCT = '423x460';

    const KHM_GPAY = '9048534800';
    const KHM_UPI = 'kcnizam@oksbi';

    const SLIDER_TYPE_STORE = 1;
    const SLIDER_TYPE_CLINIC = 2;
    const SLIDER_TYPE_BRAND = 3;
    const IMAGE_INDIVIDUAL_BRAND = '1200x200';

    const FED_FORM_ACTION='https://ipg.in.worldline.com/doMEPayRequest'; //https://cgt.in.worldline.com/ipg/doMEPayRequest
    const GET_TRANS_STATUS='https://ipg.in.worldline.com/getTransactionStatus'; //https://cgt.in.worldline.com/ipg/getTransactionStatus
    const CANCEL_TRANS_API='https://ipg.in.worldline.com/doCancelRequest'; //https://cgt.in.worldline.com/ipg/doCancelRequest
    const REFUND_TRANS_API='https://ipg.in.worldline.com/doRefundRequest'; //https://cgt.in.worldline.com/ipg/doRefundRequest
    const ENTRUSTROOTCERTIFICATEAUTH='\public\EntrustRootCertificationAuthority-G2.crt';
    const FED_MID='WL0000000010319'; //WL0000000027698
    const FED_METRANSREQTYPE='S';
    const FED_ENCRYPTION_KEY='295866f7a5c47531c2254ec5da53d73e'; //6375b97b954b37f956966977e5753ee6
    const FED_CURRENCY='INR';


    public static function get_cust_sess() {
        return session('cust_phone_auth');
    }

    public static function otp()
    {
        $otp = rand(100000, 999999);
        return $otp;
    }

    public static function district_name($id) {
        $district = DB::table('districts')->where('id',$id)->value('name');
        return $district;
    }

    public static function state_name($id) {
        $state = DB::table('states')->where('id',$id)->value('name');
        return $state;
    }

    protected  static $sale_status = [
        self::SALE_STATUS_NEW => "New",
        2 => "Accepted",
        3 => "On Hold",
        4 => "Dispatched",
        5 => "Returned",
        self::SALE_STATUS_CANCELLED  => "Cancelled",
        self::SALE_STATUS_CLOSED => "Closed",
    ];
    public static function saleStatus()
    {
        return static::$sale_status;
    }

    protected  static $slider_type = [
        self::SLIDER_TYPE_STORE => "Store",
        self::SLIDER_TYPE_CLINIC => "Clinic",
        self::SLIDER_TYPE_BRAND => "Brand",
    ];
    public static function slider_type()
    {
        return static::$slider_type;
    }

    protected  static $profit_types = [
        self::PROFIT_TYPE_PERCENTAGE => "Percentage",
        self::PROFIT_TYPE_MARGIN => "Margin",
        self::PROFIT_TYPE_SELLINGRATE => "Selling Rate",
    ];
    public static function profit_types()
    {
        return static::$profit_types;
    }

    public static function productShortDescription($id) {

        $product = Product::findOrFail($id);
        $description = Str::limit($product->description, $limit = 60, $end = '...');
        return $description;
    }

    public static function getProductUuid($id) {
        $product = Product::findOrFail($id);
        return $product->uuid;
    }

    public static function getProductSlug($id) {
        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Product')->first();
        return $all_slug->slug;
    }

    public static function getCategoryName($id) {
        $category_name = ProductType::where('id',$id)->value('name');
        return $category_name;
    }

    public static function get_stock($product,$type) {
        $stock = TypeProductPivot::where('type_id',$type)->where('product_id',$product)->first();
        $stock_quantity = $stock->stock;
        return $stock_quantity;
    }

    public static function getDeliveryUnit($id) {
        $product = Product::findOrFail($id);
        $unit = (!empty($product->delivery_unit)) ? $product->delivery_unit : 0;
        return $unit;
    }

    public static function getSingleDeliveryCharge($id) {
        $product = Product::findOrFail($id);
        $min = (!empty($product->delivery_min)) ? $product->delivery_min : 0;
        $max = (!empty($product->delivery_max)) ? $product->delivery_max : 0;
        $delivery = ['min'=>$min,'max'=>$max];
        return $delivery;
    }

    public static function getDeliveryCharge() {

        $minNeededPrice = Self::settings('minimum_to_delivery_charge');
        $minDeliverycharge = Self::settings('delivery_charge');
        $cartTotal = Cart::getTotal();

        if(session()->has('kerala_h_m_ship_option')) {
            $deliveryCharge = 0;
            $deliveryDisplay = '';
        }else {
            if(empty($minDeliverycharge)) {
                $deliveryCharge = 0;
                $deliveryDisplay = 'Free Shipping';

            }else {
                if(($cartTotal < $minNeededPrice) && ($cartTotal!=0)) {

                    $cartCollection = Cart::getContent();
                    $total_delivery = 0;
                    foreach($cartCollection as $index => $cartitem) {
                        $item_delivery = $minDeliverycharge * self::getDeliveryUnit($cartitem->id) * $cartitem->quantity;
                        if($item_delivery < self::getSingleDeliveryCharge($cartitem->id)['min']) {
                            $actualDelivery = self::getSingleDeliveryCharge($cartitem->id)['min'];
                        }elseif($item_delivery > self::getSingleDeliveryCharge($cartitem->id)['max']) {
                            $actualDelivery = self::getSingleDeliveryCharge($cartitem->id)['max'];
                        }else {
                            $actualDelivery = $item_delivery;
                        }

                        $total_delivery += $actualDelivery;
                    }

                    $deliveryCharge = $total_delivery;
                    $deliveryDisplay = 'Free Shipping';
                }
                else {
                    $deliveryCharge = 0;
                    $deliveryDisplay = 'Free Shipping';
                }

            }
        }

        $data['cost'] = $deliveryCharge;
        $data['display'] = $deliveryDisplay;
        return $data;
    }

    public static function getAffiliatePrice($product_id,$type_id) {

        $type = TypeProductPivot::where('type_id',$type_id)->where('product_id',$product_id)->first();

        $prices = $type->prices;

        $price = [];
        $price['cost'] = $prices->min('price');
        $price['khm'] = $prices->max('price');
        return $price;
    }

    public static function getParticularAffiliatePrice($profit,$product_id,$type_id,$profit_type) {

        $user = User::find(Auth::id());
        $affiliate_id = $user->affiliate->id;


        $cost = self::getAffiliatePrice($product_id,$type_id)['cost'];
        if($profit_type == self::PROFIT_TYPE_PERCENTAGE) {
            $price =  round($cost + ($cost * ($profit/100)),2);
        }else if($profit_type == self::PROFIT_TYPE_MARGIN) {
            $price =  round($cost + $profit,2);
        }else {
            $price =  round($profit,2);
        }

        return $price;
    }

    public static function settings($term) {
        $value = Setting::where('term', $term)->value('value');
        return $value;
    }

    protected  static $courier = [
        1 => ['name'=>'DTDC','website'=>'http://www.dtdc.in/tracking/tracking_results.asp'],
        2 => ['name'=>'Speed & Safe','website'=>'http://www.speedandsafe.com/cn_tracking.php'],
        3 => ['name'=>'The Professional Couriers','website'=>'http://www.tpcindia.com/']
    ];
    public static function courier()
    {
        return static::$courier;
    }


    // public static function clinic_types()
    // {
    //     $clinic_types=ClinicType::pluck('name','id');
    //     return $clinic_types;
    // }

    public static function currencyToWords(float $number)
    {
        $no = floor($number);
        $decimal = round($number - ($no), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise_pre = !empty($Rupees) ? ' and ' : '';
        $paise = ($decimal) ? $paise_pre . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? 'Rupees ' . $Rupees : '') . $paise . ' only' ;
    }

    public static function addUnderScore($data)
    {
        return empty($data) ? '' : $data . '_';
    }

    public static function cleanString($string) {
        $string = str_replace(' ','_', $string); // Replaces all spaces with underscore.
        $string = str_replace('-_','_', $string);
        $string = str_replace('-','_', $string);
        $string = str_replace('__','_', $string);

        return preg_replace('/[^A-Za-z.0-9\-_]/', '', $string); // Removes special chars.
    }

    public static function getImageDimension($imgUploadSize) {
        if(!empty($imgUploadSize)) {
            $imgUploadSizeArray = explode('x', $imgUploadSize);
            $imgUploadSizeW = intval($imgUploadSizeArray[0]);
            $imgUploadSizeH = intval($imgUploadSizeArray[1]);
        }else {
            $imgUploadSizeW = Self::DEFAULT_IMAGE_W;
            $imgUploadSizeH = Self::DEFAULT_IMAGE_H;
        }

        $dimensions = ['width'=>$imgUploadSizeW, 'height'=>$imgUploadSizeH];
        return $dimensions;
    }



}
