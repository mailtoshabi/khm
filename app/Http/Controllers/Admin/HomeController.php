<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Brand;
use App\Models\OneclickPurchase;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use App\Models\Website\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = User::find(Auth::id());
        $oneclick_count = '';

        if($user->hasRole(['admin'])) {
            $product_count = Product::all()->count();
            $sale_count = Sale::all()->count();
            $customer_count = Customer::all()->count();
        }else if($user->hasRole(['affiliate'])) {
            $affiliate_id = $user->affiliate->id;
            $affiliate = Affiliate::findOrFail($affiliate_id);
            $product_count = $affiliate->products()->count();

            $sale_count = Sale::where('user_id',Auth::id())->count();
            $oneclick_count = OneclickPurchase::where('user_id',Auth::id())->count();
            $customer_count = '';
        }else if($user->hasRole(['brand'])) {
            /*$product_count = Product::where('user_id',Auth::user()->id)->get()->count();*/
            $brand = Brand::where('user_id',Auth::user()->id)->first();
            $product_count = Product::whereHas('brands' , function($q) use($brand) {
                $q->where('brands.id', $brand->id);
            })->get()->count();

            $sale_count = '';
            $customer_count = '';
        }

        else {
            $product_count = '';
            $sale_count = '';
            $customer_count = '';
        }

        $store_count = Affiliate::all()->count();
        return view('admin.pages.home',['sale_count'=>$sale_count,'customer_count'=>$customer_count,'product_count'=>$product_count,'store_count'=>$store_count,'oneclick_count'=>$oneclick_count]);
    }

    public function showChangePasswordForm()
    {
        return view('auth.passwords.reset');
    }

    public function test()
    {
        $product = Product::with('categories')->find(2);
        $product_category_array = $product->categories;
        foreach($product_category_array as $product_category) {
            $product_categories[] = $product_category->id;
        }
        return $product_categories;
    }
}
