<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    protected $fillable = ['customer_id','order_no','pay_method','is_paid','sub_total','delivery_charge','delivery_type','status','address','courier','courier_track','delivery','is_cancelled_customer','utr_no','is_utr_cust','sms_content'];

    protected $casts = ['address' => 'array','is_paid' => 'boolean','is_cancelled_customer' => 'boolean','is_utr_cust' => 'boolean','delivery_type' => 'boolean'];

    public function customer()
    {
        return $this->belongsTO('App\Models\Website\Customer', 'customer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTO('App\Models\User', 'user_id', 'id');
    }

    public function sale_details() {
        return $this->hasMany('App\Models\SaleDetail','sale_id', 'id');
    }

    public function grand_total()
    {
        //Todo : Delete this if the function not using anywhere.
        $saleDetails = SaleDetail::findOrFail($this->id);

        /*$sales = DB::table('order_lines')
            ->join('orders', 'orders.id', '=', 'order_lines.order_id')
            ->select(DB::raw('sum(order_lines.quantity*order_lines.per_qty) AS total_sales'))
            ->where('order_lines.product_id', $product->id)
            ->where('orders.order_status_id', 4)
            ->first();*/

        /*https://laravel.io/forum/07-30-2014-sum-of-two-columns-multiplied-in-eloquent*/

        return $this->belongsTO('App\Models\Product', 'product_id', 'id');
    }

    public function salesCountYear() {
        $now = Carbon::now();
        $current_year = $now->year;
        $salesCountYear = DB::table('sales')
            ->whereYear('created_at', $current_year)->count();
        return $salesCountYear;
    }

    public function nextOrderNumber() {
        $code = "KHM";
        $now = Carbon::now();
        $current_year = $now->year;
        $nextToTotalCount = $this->salesCountYear() + 1;
        $nextToTotalCount = str_pad($nextToTotalCount,4,0,STR_PAD_LEFT);
        $nextOrderNumber = $code . '-' . $current_year . '-' .  $nextToTotalCount;

        return $nextOrderNumber;
    }

    /*public function delivery_charge() {
        $minNeededPrice = Setting::where('term', 'minimum_to_delivery_charge')->value('value');
        $minDeliverycharge = Setting::where('term', 'delivery_charge')->value('value');
        $deliveryCharge = $this->sub_total < $minNeededPrice ? $minDeliverycharge : 0;
        return $deliveryCharge;
    }*/
}
