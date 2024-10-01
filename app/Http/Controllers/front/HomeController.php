<?php

namespace App\Http\Controllers\front;

use App\Models\Affiliate;
use App\Models\AllSlug;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
// use App\Models\Clinic;
// use App\Models\Doctor;
use App\Models\OneclickPurchase;
// use App\Models\Prescription;
use App\Models\PriceDetail;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Store;
use App\Models\Subscribe;
// use App\Models\Treatment;
use App\Models\TypeProductPivot;
use App\Models\User;
use App\Models\Website\Customer;
use App\Models\Website\CustomerDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Http\Utilities\Utility;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\InstaMojoController as InstaMojoController;
use App\Http\Controllers\SmsApiController as SmsApiController;
use App\Http\Controllers\SmsFastMsgController as SmsFastMsgController;
use App\Http\Controllers\AWLMEAPIController as AWLMEAPI;
use App\Http\Controllers\ReqMsgDTOController as ReqMsgDTO;
use App\Http\Controllers\ResMsgDTOController as ResMsgDTO;
use Carbon\Carbon;

/*use setasign\Fpdi\Fpdi;*/
class HomeController extends Controller
{
    public  function index () {
        /*$banners = Banner::where('is_active',1)->orderBy('order_no','asc')->get();*/
        $banners = Banner::where('is_active',1)->where(function($query) {
            $query->where('user_id',Utility::KHM_USER_ID);
        })->orderBy('order_no','asc')->get();

        $offerCatId = Utility::CATEGORY_ID_OFFER;
        $offerProudcts = Product::where('is_active',1)->whereHas('categories', function($query) use($offerCatId) {
            $query->where('categories.id', $offerCatId);
        })->take(8)->latest()->get();

        foreach($offerProudcts as $offerProudct) {
            $slug_offer = AllSlug::where('causer_id',$offerProudct->id)->where('causer_type', 'App\Models\Product')->first();
            $slug_ofr = $slug_offer->slug;
            $offerProudct->slug = $slug_ofr;
        }

        $mainCategories = Category::where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')->get();

        foreach($mainCategories as $mainCategory) {
            foreach($mainCategory->products as $allProduct) {
                $all_slug = AllSlug::where('causer_id',$allProduct->id)->where('causer_type', 'App\Models\Product')->first();
                $slug_al = $all_slug->slug;
                $allProduct->slug = $slug_al;
            }
            $all_slug_mainCategory = AllSlug::where('causer_id',$mainCategory->id)->where('causer_type', 'App\Models\Category')->first();
            $mainCategory->slug = $all_slug_mainCategory->slug;
        }

        $featuredProudcts = Product::where('is_active',1)->where('is_featured',1)->take(8)->latest()->get();
        foreach($featuredProudcts as $featuredProudct) {
            $slug_featured = AllSlug::where('causer_id',$featuredProudct->id)->where('causer_type', 'App\Models\Product')->first();
            $slug = $slug_featured->slug;
            $featuredProudct->slug = $slug;
        }


        return view('pages.home',['banners'=>$banners, 'offerProudcts'=>$offerProudcts,'featuredProudcts'=>$featuredProudcts,'mainCategories'=>$mainCategories]);
    }

    public  function about_us () {
        return view('pages.about');
    }

    public  function payments () {
        return view('pages.payments');
    }

    public  function disclaimer () {
        return view('pages.disclaimer');
    }

    public  function shipping () {
        return view('pages.shipping');
    }

    public  function cancellation () {
        return view('pages.cancellation');
    }

    public  function privacy_policy () {
        return view('pages.privacy_policy');
    }

    public  function terms_conditions () {
        return view('pages.terms_conditions');
    }
    public  function affiliate () {
        return view('pages.affiliate');
    }
    public  function affiliates () {
        $sliders = Slider::where('is_active',1)->where('type',Utility::SLIDER_TYPE_STORE)->orderBy('order_no','asc')->get();

        $affiliates = User::whereHas('roles' , function($q){
            $q->where('id', Utility::AFFILIATE_ROLE_ID);
        })->where('is_active',1)->latest()->get();

        foreach($affiliates as $affiliate) {
            $all_slug = AllSlug::where('causer_id',$affiliate->affiliate->id)->where('causer_type', 'App\Models\Affiliate')->first();
            $slug = $all_slug->slug;
            $affiliate->slug = $slug;
        }
        return view('pages.affiliates',['affiliates' => $affiliates,'sliders' => $sliders]);
    }
    public  function online_pay () {
        return view('pages.online_pay');
    }

    public  function my_orders () {
        $customer_id = Auth::guard('customer')->user()->id;
        $sales = Sale::where('customer_id',$customer_id)->where('status','!=',Utility::SALE_STATUS_CANCELLED)->latest()->paginate(10);
        return view('pages.myorders',['sales'=>$sales]);
    }

    public function utr_update(Request $request) {
        $sale = Sale::find($request->sale_id);
        if($sale->is_paid) {

        }else {
            $input['utr_no'] = $request->utr_no;
            $input['is_utr_cust'] = 1;
            $sale->fill($input)->save();
            return response()->json(['utr' => $request->utr_no]);
        }
    }

    public  function settings_account () {
        return view('pages.account_settings');
    }

    public  function delete_account () {
        return view('pages.delete_account');
    }

    public  function delete_account_act (Request $request) {
        $customer_id = $request->customer_id;
        $sales = Sale::where('customer_id',$customer_id)
            ->where(function ($query) {
                $query->where('status',Utility::SALE_STATUS_CANCELLED)
                    ->orWhere('status',Utility::SALE_STATUS_CLOSED);
            })
            ->get();
        if($sales->count()==0) {
            Customer::find($customer_id)->delete();
            $request->session()->flush();
            $success_route = route('index');
            return response()->json($success_route);
        }else {
            $success_route = route('delete.account',['error_msg',1]);
            return response()->json($success_route);
        }

    }

    public  function contact () {
        return view('pages.contact');
    }

    public function contact_send(Request $request) {
        $subject = 'Enquiry via contact form of Kerala Health Mart';
        $to = "keralahealthmart@gmail.com"; //
        $from = "noreply@keralahealthmart.com";
        // $send = Mail::send('mails.contact_us', ['data' => $request->all()], function ($message) use ($from, $to, $subject) {
        //     $message->from($from, config('app.name','Kerala Health Mart'));
        //     $message->to($to);
        //     $message->subject($subject);
        //     return 1;

        // });
    }

    public function dealer_send(Request $request) {
        $subject = 'Enquiry of Dealership from ' . $request->name;
        $to = "keralahealthmart@gmail.com"; //
        $from = "noreply@keralahealthmart.com";
        // $send = Mail::send('mails.dealrship', ['data' => $request->all()], function ($message) use ($from, $to, $subject) {
        //     $message->from($from, config('app.name','Kerala Health Mart'));
        //     $message->to($to);
        //     $message->subject($subject);
        //     return 1;

        // });
    }

    public  function category_all () {
        /*$categories = Category::where('parent',0)->whereNotIn('id', [11])->orderBy('id','desc')->get();
        foreach($categories as $category) {
            $category->childs = $category->child_categories();
        }
        */
        $categories = Category::where('is_active',1)->whereNotIn('id', [11])->orderBy('id','desc')->get();
        foreach($categories as $category) {
            // foreach($category->childs as $child) {
            //     $all_slug_child = AllSlug::where('causer_id',$child->id)->where('causer_type', 'App\Models\Category')->first();
            //     $slug_child = $all_slug_child->slug;
            //     $child->slug = $slug_child;
            // }
            $all_slug_cat = AllSlug::where('causer_id',$category->id)->where('causer_type', 'App\Models\Category')->first();
            $slug_cat = $all_slug_cat->slug;
            $category->slug = $slug_cat;
        }

        // $mainCategories = Category::where('is_active',1)->has('parents',0)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')->get();
        // foreach($mainCategories as $mainCategory) {
        //     $child_product_array=[];
        //     $child_product_id_array=[];
        //     foreach($mainCategory->childs as $child_category) {
        //         $child_products = Category::findOrFail($child_category->id)->products;
        //         if(!empty(json_decode($child_products))) {
        //             foreach($child_products as $child_product) {
        //                 if(!in_array($child_product->id,$child_product_id_array)) {
        //                     array_push($child_product_array,$child_product);
        //                     $child_product_id_array[] = $child_product->id;
        //                     $child_product->price = $child_product->min_price();
        //                     $child_product->min_mrp = $child_product->min_mrp();
        //                 }
        //             }

        //         }
        //     }
        //     $mainCategory->products = $child_product_array;

        // }
        // foreach($mainCategories as $mainCategory) {
        //     foreach($mainCategory->products as $allProduct) {
        //         $all_slug = AllSlug::where('causer_id',$allProduct->id)->where('causer_type', 'App\Models\Product')->first();
        //         $slug = $all_slug->slug;
        //         $allProduct->slug = $slug;
        //     }
        // }

        return view('pages.categories',['categories' => $categories]);
    }

    public  function sub_category_all ($id) {
        $mainCategory = Category::findOrFail($id);
            /*$category->childs = $category->child_categories();*/

        /*$child_product_array=[];
        $child_product_id_array=[];
        foreach($mainCategory->childs as $child_category) {
            $child_products = Category::findOrFail($child_category->id)->products;
            if(!empty(json_decode($child_products))) {
                foreach($child_products as $child_product) {
                    if(!in_array($child_product->id,$child_product_id_array)) {
                        array_push($child_product_array,$child_product);
                        $child_product_id_array[] = $child_product->id;
                        $child_product->price = $child_product->min_price();
                        $child_product->min_mrp = $child_product->min_mrp();

                        $all_slug = AllSlug::where('causer_id',$child_product->id)->where('causer_type', 'App\Models\Product')->first();
                        $slug = $all_slug->slug;
                        $child_product->slug = $slug;
                    }
                }

            }
        }*/



            $child_product_array=[];
            $child_product_id_array=[];
            $child_cat_ids=[];
            foreach($mainCategory->childs as $child_category) {
                $child_cat_ids[] = $child_category->id;
            }

            $child_products = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($child_cat_ids) {
                $query->whereIn('categories.id', $child_cat_ids);
            })->get();

            foreach ($child_products as $child_product) {
                if (!in_array($child_product->id, $child_product_id_array)) {
                    array_push($child_product_array, $child_product);
                    $child_product_id_array[] = $child_product->id;
                    $all_slug = AllSlug::where('causer_id',$child_product->id)->where('causer_type', 'App\Models\Product')->first();
                    $slug_al = $all_slug->slug;
                    $child_product->slug = $slug_al;
                    $child_product->price = $child_product->min_price();
                    $child_product->min_mrp = $child_product->min_mrp();
                }
            }

        return view('pages.subcategories',['category' => $mainCategory,'products'=>$child_product_array]);
    }

    public  function category_products ($id) {
        $category = Category::findOrFail($id);

        $products = Product::where('is_active',1)->whereHas('categories', function($query) use($id) {
            $query->whereIn('categories.id', [$id]);
        })->latest()->paginate(24);

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $product->slug = $all_slug->slug;
        }
        return view('pages.products',['category' => $category, 'products'=>$products]);
    }

    public  function offer_products () {
        $id = Utility::CATEGORY_ID_OFFER;
        $category = Category::findOrFail($id);

        $products = Product::where('is_active',1)->whereHas('categories', function($query) use($id) {
            $query->whereIn('categories.id', [$id]);
        })->latest()->paginate(24);

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $product->slug = $all_slug->slug;
        }
        return view('pages.products',['category' => $category, 'products'=>$products]);
    }

    public  function brands () {
        $sliders = Slider::where('is_active',1)->where('type',Utility::SLIDER_TYPE_BRAND)->orderBy('order_no','asc')->get();
        $brands = Brand::where('is_active',1)->get();
        foreach($brands as $brand) {
            $all_slug = AllSlug::where('causer_id',$brand->id)->where('causer_type', 'App\Models\Brand')->first();
            $brand->slug = $all_slug->slug;
        }
        return view('pages.brands',['brands' => $brands,'sliders' => $sliders]);
    }

    /*public  function brand_products ($id) {
        $brand = Brand::findOrFail($id);

        $products = Product::whereHas('brands', function($query) use($id) {
            $query->whereIn('brands.id', [$id]);
        })->latest()->paginate(24);

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $product->slug = $all_slug->slug;
        }

        return view('pages.brand_products',['brand' => $brand, 'products'=>$products]);
    }*/

    public  function search_results (Request $request) {
        $cat_id = $request->cat_id;
        // $subcat_id = $request->subcat_id;
        $term = $request->has('term')?$request->term:'';
        $term_display = $request->has('term')?$request->term:'';

        $allproducts = Product::where('is_active',1);
        if ($request->has('term')) {
            $allproducts->where('name', 'like', '%'.$term.'%');
        }

        if ($request->has('cat_id')&&!empty($cat_id)) {
            $allproducts->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->cat_id);
            });
        }

        $allproducts = $allproducts->get();

        foreach($allproducts as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $slug = $all_slug->slug;
            $product->slug = $slug;
        }

        return view('pages.search_products',['products' => $allproducts, 'term'=>$term_display, 'selected_cat' => $cat_id]);
    }

    public  function featured_products () {
        $products = Product::where('is_featured',1)->where('is_active',1)->latest()->get();
        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $slug = $all_slug->slug;
            $product->slug = $slug;
        }
        return view('pages.featured',['products' => $products]);
    }

    /*public  function product_detail ($uuid) {
         $product = Product::where('uuid',$uuid)->first();
         if($product) {
             $id = $product->id;
             $categories = $product->categories;
             $cat_ids = [];
             foreach($categories as $category) {
                 $cat_ids[] = $category->id;
             }
             $relatedProducts = Product::where('is_active',1)->whereHas('categories', function($query) use($cat_ids,$id) {
                 $query->whereIn('categories.id', $cat_ids)->whereNotIn('products.id', [$id]);
             })->latest()->get();

             return view('pages.product_detail', ['product' => $product, 'relatedProducts'=>$relatedProducts]);
         }
     }*/

    public function oneclick_purchase (Request $request) {
        $rules = [
            'phone' => 'required|max:255',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {

            }
        }else {
            $purchase = new OneclickPurchase;
            $input = $request->all();
            $input['user_id'] = Utility::KHM_USER_ID;
            $purchase->fill($input)->save();
        }
        return $purchase;
    }

    // public function prescription (Request $request) {
    //     $rules = [
    //         'phone_prescription' => 'required|max:255',
    //     ];
    //     $messages = [
    //         'required' => 'The :attribute field is required.',
    //     ];
    //     $validator = Validator::make($request->all(), $rules, $messages);
    //     if ($validator->fails()) {
    //         if($request->ajax()) {
    //             return response()->json($validator->errors(), 422);
    //         } else {

    //         }
    //     }else {
    //         $prescription = new Prescription;
    //         $input = $request->all();
    //         $input['user_id'] = Utility::KHM_USER_ID;
    //         $prescription->fill($input)->save();

    //         if($request->hasFile('image_prescription')) {
    //             $image_prescription = $request->file('image_prescription');
    //             $image_prescription_name = $prescription->id . '_' . str_replace(' ','_',$image_prescription->getClientOriginalName());
    //             $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Prescription::FILE_DIRECTORY);
    //             if (!File::exists($destinationPath)) {
    //                 File::makeDirectory($destinationPath, $mode = 0777, true, true);
    //             }
    //             $realPath = $_FILES['image_prescription']['tmp_name'];
    //             $contents = file_get_contents($realPath);
    //             $image_prescription_path = Prescription::FILE_DIRECTORY . '/'. $image_prescription_name;
    //             Storage::disk('prescriptions')->put($image_prescription_name, $contents);
    //             $prescription->image_prescription = $image_prescription_path;
    //             $prescription->save();
    //         }
    //     }
    //     return $prescription;
    // }

    public function get_price (Request $request) {
        
        $product_id = $request->product_id;
        $type_size = $request->type_size;
        $quantity = (int)$request->quantity;

        $type = TypeProductPivot::where('type_id',$type_size)->where('product_id',$product_id)->first();
        $type_id = $type->id;

        if(\Cart::isEmpty()) {
            $total_quantity_added = $quantity;
        }
        else {
            $cartCollection = \Cart::getContent();
            if ($cartCollection->has($product_id.'_'.$type_size)) {
                $cartItem = $cartCollection->get($product_id.'_'.$type_size);
                $total_quantity_added = (int)$cartItem->quantity+$quantity;
            }
            else {
                $total_quantity_added = $quantity;
            }
        }

        if($type->stock == 0) {
            $type->stock_status = 0;
        }else if($type->stock < $total_quantity_added) {
            $type->stock_status = 0;
        }else {
            $type->stock_status = 1;
        }
        $price = PriceDetail::where('tp_pivot_id',$type_id);
        $price = $price->where(function ($query) use ($quantity) {
            $query->where('quantity_from', '<=', $quantity);
        });
        $price = $price->orderBy('quantity_from','desc')->first(['price']);
        if(!empty($type->mrp) && ($type->mrp != $price->price) && ($type->mrp!=0)) {
            $discount = (($type->mrp-$price->price)/$type->mrp)*100;
            $type->discount = round($discount,0);
        }else {
            $type->discount = 0;
            $type->mrp = 0;
        }


        $prices = PriceDetail::where('tp_pivot_id',$type_id)->get();
        $allPrices = '';
        $allPrices .= '<b style="margin:0px; padding-bottom: 5px;">QUANTITY DISCOUNT</b> <br>' .
            '<table class="table table-bordered">
                            <tr>
                                <th>
                                Qty
                                </th>
                                <th>
                                Rate/Each
                                </th>
                            </tr>';
        foreach($prices as $single_price) {
            if(!empty($single_price->quantity_to)) {
                /*$allPrices[] = 'Buy Qty ' . $single_price->quantity_from . '-' . $single_price->quantity_to . ' @ ' . '<i class="fa fa-inr"></i>' . $single_price->price . '<br>';*/
                if($single_price->quantity_from==$single_price->quantity_to) {
                    $allPrices .= '<tr><td>' .
                        $single_price->quantity_from .
                        '</td>
                                <td>
                                <i class="fa fa-inr"></i>' . $single_price->price .
                        '</td></tr>';
                }else {
                    $allPrices .= '<tr><td>' .
                        $single_price->quantity_from . '-' . $single_price->quantity_to .
                        '</td>
                                <td>
                                <i class="fa fa-inr"></i>' . $single_price->price .
                        '</td></tr>';
                }
            }else {
                /*$allPrices[] = 'Buy Qty above ' . $single_price->quantity_from . ' @ ' . '<i class="fa fa-inr"></i>' . $single_price->price . '<br>';*/
                $allPrices .= '<tr><td>' .
                    $single_price->quantity_from . '+' .
                    '</td>
                                <td>
                                <i class="fa fa-inr"></i>' . $single_price->price .
                    '</td></tr>';
            }
        }

        $allPrices .= '</table>';


        return ['price'=>$price->price, 'quantity' => $quantity, 'type' => $type, 'prices'=>$allPrices];

    }

    public function get_stock (Request $request) {
        $product_id = $request->product_id;
        $type_size = $request->type_size;
        $quantity = (int)$request->quantity;

        $stock = TypeProductPivot::where('type_id',$type_size)->where('product_id',$product_id)->first();
        $stock_quantity = $stock->stock;
        return $stock_quantity;
    }

    public function actual_price ($product_id,$type_size, $quantity) {
        $type = TypeProductPivot::where('type_id',$type_size)->where('product_id',$product_id)->first();
        $type_id = $type->id;
        $price = PriceDetail::where('tp_pivot_id',$type_id);
        $price->where(function ($query) use ($quantity) {
            $query->where('quantity_from', '<=', $quantity);
        });
        $price = $price->orderBy('quantity_from','desc')->first(['price']);
        return $price->price;
    }

    public function cart_show() {
        if(\Cart::isEmpty()) {
            $cartCollection = '';
        }
        else {
            $cartCollection = \Cart::getContent();
            $cartCollection = json_decode($cartCollection);
        }
        return view('pages.cart',['cartCollection'=>$cartCollection, 'grandTotal'=>\Cart::getTotal()]);
    }

    public function cart_add(Request $request) {
        $product_id = $request->id;
        $type_size = $request->type;
        $requested_quantity = (int)$request->quantity;
        if(\Cart::isEmpty()) {
            $quantity = $requested_quantity;
            $total_quantity_added = $requested_quantity;
        }else {
            $cartCollection = \Cart::getContent();
            if ($cartCollection->has($product_id.'_'.$type_size)) {
                $cartItem = $cartCollection->get($product_id.'_'.$type_size);
                $quantity = (int)$cartItem->quantity+$requested_quantity;
            }
            else {
                $quantity = $requested_quantity;
            }
            $total_quantity_added = $quantity;
        }

        $type = TypeProductPivot::where('type_id',$type_size)->where('product_id',$product_id)->first();

        if($type->stock == 0) {
            $type->stock_status = 0;
        }else if($type->stock < $total_quantity_added) {
            $type->stock_status = 0;
        }else {
            $type->stock_status = 1;
        }

        $actual_price = $this->actual_price ($request->id,$request->type, $quantity);
        if($type->stock_status ==1) {
            \Cart::add(array(
                'id' => (int)$request->id,
                'name' => (string)$request->name,
                'price' => $actual_price,
                'quantity' => (int)$request->quantity,
                'attributes' => ['type' => $request->type, 'image' => $request->product_image]
            ));
        }

        $cartCollection2 = \Cart::getContent();

        return response()->json([
            'cart' => $cartCollection2, 'cart_total' => \Cart::getTotalQuantity(), 'stock_status'=> $type->stock_status
        ]);
    }

    public function cart_update(Request $request, $item) {

        $quantity = (int)$request->quantity;
        $cartItem = \Cart::get($item);
        /*return $cartItem;*/

        $type = TypeProductPivot::where('type_id',$cartItem->attributes->type)->where('product_id',$cartItem->id)->first();

        if($type->stock == 0) {
            $stock_status = 0;
        }else if($type->stock < $quantity) {
            $stock_status = 0;
        }else {
            $stock_status = 1;
        }

        $actual_price = $this->actual_price ($cartItem->id,$cartItem->attributes->type, $quantity);

        if($stock_status ==1) {
            \Cart::update($item, array(
                'quantity' => array(
                    'relative' => false,
                    'value' => $quantity
                ),
                'price' => $actual_price
            ));
            return ['price'=>$actual_price,'quantity'=>$quantity, 'stock' => $type->stock, 'updated'=>1];
        }else {
            return ['price'=>$cartItem->price,'quantity'=>$cartItem->quantity, 'stock' => $type->stock, 'updated'=>0];
        }
    }

    public function cart_delete($item) {
        $remove = \Cart::remove($item);
        if(\Cart::isEmpty()) return 1;
        else return 0;
    }

    public  function shipping_options (Request $request, $id) {
        $request->session()->forget('kerala_h_m_ship_option');
        if($id == 1) {
            session(['kerala_h_m_ship_option' => $id]);
        }
        return response()->json(['shipping_option' => $id]);
        /*$otp = $request->session()->get('kerala_h_m_o_t_p');*/
    }

    public function refresh_cart() {
        $delivery = Utility::getDeliveryCharge();
        $data['ship_option'] = session()->has('kerala_h_m_ship_option') ? 1 : 0;
        $data['delivery'] = $delivery;
        $data['total_quantity'] = \Cart::getTotalQuantity();
        $data['total_amount'] = \Cart::getTotal();
        $data['amount_payable'] = round($delivery['cost'] + $data['total_amount'],2);

        return $data;
    }

    public function cart_clear(Request $request) {
        $clear = \Cart::clear();
        $request->session()->forget('kerala_h_m_ship_option');
        if($clear) return 1;
        else return 0;
    }

    public function checkout_login() {
        return view('pages.checkout_login',['grandTotal'=>\Cart::getTotal()]);
    }

    public function checkout_address() {
        if(\Cart::isEmpty()) return redirect()->route('index');
        $customer_id = Auth::guard('customer')->user()->id;
        $customerDetails = CustomerDetail::where('customer_id',$customer_id)->first();
        $states = DB::table('states')->select('id','name')->get();
        return view('pages.checkout_address',['states'=> $states, 'customerDetails' => $customerDetails, 'grandTotal'=>\Cart::getTotal()]);
    }

    public function checkout_address_store(Request $request) {
        $validator = $this->address_validator($request->all());
        $customer_id = Auth::guard('customer')->user()->id;
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('checkout.address')
                    ->withErrors($validator)
                    ->withInput();
            }
        }else {
            $customerDetails = CustomerDetail::where('customer_id',$customer_id)->first();

            $address = ['phone_alt' => $request->phone_alt, 'address' => $request->address, 'place' => $request->place, 'pincode' => $request->pincode, 'city' => $request->city, 'district' => $request->district, 'state' => $request->state];
            $customerDetails->fill([
                'name' => $request->name,
                'address' => $address,
                'gstin' => $request->gstin
            ]);
            $customerDetails->save();

            if(!empty($request->phone)) {
                $customer = Customer::find($customer_id);
                $customer->fill([
                    'phone' => $request->phone,
                ]);
                $customer->save();
            }

            return $customerDetails;
        }

    }

    protected function address_validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'place' => 'required',
            'pincode' => 'required|digits:6',
            'district' => 'required',
            'state' => 'required'
        ]);
    }

    public function distric_list(Request $request) {

        $customer_id = Auth::guard('customer')->user()->id;
        $customerDetails = CustomerDetail::where('customer_id',$customer_id)->first();
        $customer_district_id = !empty($customerDetails->address)?$customerDetails->address['district']:'';
        $districts = DB::table('districts')->select('id','name')->where('state_id',$request->state_id)->get();
        $data[]= '<option value="">Select</option>';
        foreach($districts as $district) {
            $selected = !empty($customer_district_id) && ($district->id == $customer_district_id) ? 'selected' : '';
            $data[] = '<option value="' . $district->id . '"' . $selected . ' >'. $district->name . '</option>';
        }
        return $data;
    }

    public function subcategory_list(Request $request) {

        $category = Category::find($request->category_id);
        $data[]= '';
        foreach($category->childs as $child) {
            $selected = ($request->has('subcategory_id')) && ($request->subcategory_id == $child->id) ? 'selected' : '';
            $data[] = '<option value="' . $child->id . '"' . $selected . ' >'. $child->name . '</option>';
        }
        return $data;
    }

    public function payment_options() {
        if(\Cart::isEmpty()) return redirect()->route('index');
        return view('pages.payment_options',['grandTotal'=>\Cart::getTotal()]);
    }

    public function payment_options_store(Request $request) {

        /*$success_route = route('checkout.payment.success');
        return response()->json($success_route);*/

        if($request->payment_option != Utility::PAYMENT_ONLINE) {
            /*$rules = [
                'g-recaptcha-response' => 'required',
            ];
            $messages = [
                'g-recaptcha-response.required' => 'Verify that you are not a robot.',
            ];*/

            $rules = [
                /*'captcha' => 'required|captcha',*/
            ];
            $messages = [
                /*'captcha.required' => 'Enter the captcha characters.',
                'captcha.captcha' => 'Invalid captcha characters.'*/
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

        }

        $customer_id = Auth::guard('customer')->user()->id;
        $customer_details = CustomerDetail::where('customer_id',$customer_id)->first();

        $cartCollection = \Cart::getContent();
        $cartCollection = json_decode($cartCollection);
        if(empty($cartCollection)) {
            $success_route = route('product.cart');
            return response()->json($success_route);
        }
        $cartTotal = \Cart::getTotal();

        $delivery = Utility::getDeliveryCharge();
        $deliveryCharge = $delivery['cost'];

        foreach($cartCollection as $index => $cartitem) {
            $stock = Utility::get_stock($cartitem->id,$cartitem->attributes->type);
            if($stock < $cartitem->quantity) {
                $success_route = route('product.cart');
                return response()->json($success_route);
            }
        }

        $sale = new Sale;
        $order_id = $sale->nextOrderNumber();

        $sale->customer_id = $customer_id;
        $sale->order_no = $order_id;
        $sale->pay_method = $request->payment_option;
        $sale->sub_total = $cartTotal;
        $sale->delivery_charge = $deliveryCharge;
        $sale->delivery_type = session()->has('kerala_h_m_ship_option') ? Utility::DELIVERY_TYPE_TOPAY : Utility::DELIVERY_TYPE_PAID;
        $sale->address = $customer_details->address;
        $sale->user_id = Utility::KHM_USER_ID;
        $sale->save();

        foreach($cartCollection as $index => $cartitem) {
            $saleDetails = new SaleDetail;
            $saleDetails->sale_id = $sale->id;
            $saleDetails->product_id = $cartitem->id;
            $saleDetails->type_size = $cartitem->attributes->type;
            $saleDetails->quantity = $cartitem->quantity;
            $saleDetails->price = $cartitem->price;

            $stock = Utility::get_stock($cartitem->id,$cartitem->attributes->type);
            $new_stock = $stock - $cartitem->quantity;
            $stock_details = TypeProductPivot::where('type_id',$cartitem->attributes->type)->where('product_id',$cartitem->id)->first();
            $stock_details->stock = $new_stock;

            $saleDetails->save();
            $stock_details->save();
        }

        $amount= $sale->sub_total+$deliveryCharge;
        $amount_paisa= $amount*100;
        $purpose = $sale->order_no;
        $phone = Auth::guard('customer')->user()->phone;
        $email = Auth::guard('customer')->user()->email;
        $name = $customer_details->name;
        // $redirectUrl = route('checkout.payment.success');

        //SEND SMS START
        // $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
        // $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
        // $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
        // $smsText = config('app.name') . ': Your order ' . $sale->order_no . ' amounting to Rs. ' . $amount . ' placed. We will send you an update when the order is shipped.';
        // $mblno = Setting::IND_CODE . $sale->customer->phone;
        //SEND SMS END

        if($request->payment_option == Utility::PAYMENT_ONLINE) {
            $marchantId = Utility::marchantId;
            $consumerId = encrypt($customer_id);
            $salt = Utility::FEDSALT;
            /*$sendPayment = new InstaMojoController($amount,$purpose,$phone,$email,$name,$redirectUrl);
            $createRequest = $sendPayment->createRequest();
            $success_route = route('payment.online',['request_url' => $createRequest]);*/
            //return response()->json($success_route);
            // return $this->meTrnPay($order_id,$amount_paisa,$customer_details->name,$mblno);
            return $this->fedPayProcess($marchantId,$order_id,$amount,$consumerId,$name,$phone, $email,$salt);
        }else {
            $clear = \Cart::clear();
            $request->session()->forget('kerala_h_m_ship_option');
            // $success_route = route('checkout.payment.success');

            if(!empty($sale->customer->phone)) {
                //$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');
                // $sendSMS = $this->sendsms($sale->customer->phone, $smsText);
            }

            //return response()->json($success_route);
            // return redirect()->route('checkout.payment.success');
            $message = "Payment Pending - your order is confirmed!";
            $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
            return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails, 'message'=>$message]);
        }

    }

    public function fedPayProcess($marchantId,$order_id,$amount,$consumerId,$name,$phone,$email,$salt)
    {
       $path = storage_path() . "/json/worldline_AdminData.json";
       $mer_array = json_decode(file_get_contents($path), true);

       $datastring = $marchantId . "|" . $order_id . "|" . $amount . "|" . "|" . $consumerId . "|" . $phone . "|" . $email . "||||||||||" . $salt;

        $hashVal = hash('sha512', $datastring);
        $paymentDetails = array(
            'marchantId' => $marchantId,
            'txnId' => $order_id,
            'amount' => $amount,
            'currencycode' => Utility::CURRENCY_CODE,
            'schemecode' => Utility::schemecode,
            'consumerId' => $consumerId,
            'mobileNumber' => $phone,
            'email' => $email,
            'customerName' => $name,
            'accNo' => '',
            'accountName' => '',
            'aadharNumber' => '',
            'ifscCode' => '',
            'accountType' => '',
            'debitStartDate' => '',
            'debitEndDate' => '',
            'maxAmount' => '',
            'amountType' => '',
            'frequency' => '',
            'cardNumber' => '',
            'expMonth' => '',
            'expYear' => '',
            'cvvCode' => '',
            'hash' => $hashVal
        );
        return view('payment.checkoutpage', ['payval' => $paymentDetails],compact('mer_array'));
    }

    public function fedPaycheckout(Request $request)
    {
        $response = $request->msg;
        $res_msg = explode("|",$_POST['msg']);

        $path = storage_path() . "/json/worldline_AdminData.json";
        $mer_array = json_decode(file_get_contents($path), true);
        date_default_timezone_set('Asia/Calcutta');
         $strCurDate = date('d-m-Y');


        $arr_req = array(
            "merchant" => ["identifier" => $mer_array['merchantCode'] ],
            "transaction" => [ "deviceIdentifier" => "S","currency" => $mer_array['currency'],"dateTime" => $strCurDate,
            "token" => $res_msg[5],"requestType" => "S"]
        );

        $finalJsonReq = json_encode($arr_req);

        function callAPI($method, $url, $finalJsonReq)
        {
           $curl = curl_init();
           switch ($method)
           {
              case "POST":
                 curl_setopt($curl, CURLOPT_POST, 1);
                 if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);
                 break;
              case "PUT":
                 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                 if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);
                 break;
              default:
                 if ($finalJsonReq)
                    $url = sprintf("%s?%s", $url, http_build_query($finalJsonReq));
           }
           // OPTIONS:
           curl_setopt($curl, CURLOPT_URL, $url);
           curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
           ));
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
           // EXECUTE:
           $result = curl_exec($curl);

           if(!$result){die("Connection Failure !! Try after some time.");}
           curl_close($curl);
           return $result;
        }

        $method = 'POST';
        $url = "https://www.paynimo.com/api/paynimoV2.req";
        $res_result = callAPI($method, $url, $finalJsonReq);
        $dualVerifyData = json_decode($res_result, true);

        // return view('responsepage',compact('response','res_msg','dualVerifyData'));
        $order_no = $res_msg[3];

        $sale = Sale::where('order_no',$order_no)->latest()->first();
        $customerID = encrypt($sale->customer->id);
        $saleID = encrypt($sale->id);
        $amount = $sale->sub_total + $sale->delivery_charge;
        $mblno = $sale->customer->phone;
        if($sale) {
            $sale->fed_txn_status = $res_msg[0];
            $sale->fed_txn_message = $res_msg[1];
            $sale->fed_txn_error_message = $res_msg[2];
            $sale->fed_tpsl_txn_id = $res_msg[5];
            $sale->fed_transaction_identifier = $dualVerifyData['merchantTransactionIdentifier'];
            $sale->fed_worldline_identifier = $dualVerifyData['paymentMethod']['paymentTransaction']['identifier'];
            $sale->fed_amount = $dualVerifyData['paymentMethod']['paymentTransaction']['amount'];
            $sale->fed_error_message = $dualVerifyData['paymentMethod']['paymentTransaction']['errorMessage'];
            $sale->fed_status_message = $dualVerifyData['paymentMethod']['paymentTransaction']['statusMessage'];
            $sale->fed_status_code = $dualVerifyData['paymentMethod']['paymentTransaction']['statusCode'];
            $sale->fed_date_time = !empty($dualVerifyData['paymentMethod']['paymentTransaction']['dateTime']) ? Carbon::parse($dualVerifyData['paymentMethod']['paymentTransaction']['dateTime'])->format('Y-m-d H:i:s'):null;
            if($dualVerifyData['paymentMethod']['paymentTransaction']['statusCode'] == '0300') {
                $sale->is_paid = 1;
                $sale->pay_method = Utility::PAYMENT_ONLINE;
            }else {
                $sale->is_paid = 0;
            }
            $sale->save();
        }

        $clear = \Cart::clear();
        $request->session()->forget('kerala_h_m_ship_option');
        //SEND SMS START
        // $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
        // $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
        // $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
        // $smsText = config('app.name') . ': Your order ' . $order_no . ' amounting to Rs. ' . $amount . ' placed. We will send you an update when the order is shipped.';
        //SEND SMS END
        // $sendSMS = $this->sendsms($mblno, $smsText);

        //  $customer_id = Auth::guard('customer')->user()->id;

        //  if($customer_id) {

            // $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
            // return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customerID]);
            //TODO: redirect the route instead of showing view page.
            if($sale->is_paid==1) {
                return redirect()->route('fed_payment_success',['sale' => $saleID, 'customer' => $customerID]);
            }
            else {
                return redirect()->route('fed_payment_fail',['sale' => $saleID, 'customer' => $customerID]);
            }

        //  }else {
        //     return redirect()->route('index');
        //  }
    }

    public function fed_payment_success($saleID,$customerID) {
        $sale = Sale::find(decrypt($saleID));
        $customer = Customer::find(decrypt($customerID));
        $message = "Payment Success - your order is confirmed!";
        return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customer->customer_detail, 'message'=>$message]);
    }

    public function fed_payment_fail($saleID,$customerID) {
        $sale = Sale::find(decrypt($saleID));
        $customer = Customer::find(decrypt($customerID));
        $message = "Payment Failed - your order is on pending";
        return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customer->customer_detail, 'message'=>$message]);
    }

    public function payment_success(Request $request) {
        //create an Object of the above included class
        $obj = new AWLMEAPI();

        /* This is the response Object */
        //$resMsgDTO = new ResMsgDTO();

        /* This is the request Object */
        //$reqMsgDTO = new ReqMsgDTO();

        //This is the Merchant Key that is used for decryption also
        $enc_key = Utility::FED_ENCRYPTION_KEY;

        /* Get the Response from the WorldLine */
        $responseMerchant = $_REQUEST['merchantResponse'];

        $response = $obj->parseTrnResMsg( $responseMerchant , $enc_key );

        if($response->getStatusCode()=="S") {
            // Payment was successful, mark it as successful in your database.
            // You can acess payment_request_id, purpose etc here.

            $mblno = $response->getAddField1();
            $order_no = $response->getOrderId();
            $sale = Sale::where('order_no',$order_no)->latest()->first();
            if($sale) {
                $sale->is_paid = 1;
                $sale->payment_id = $response->getPgMeTrnRefNo();
                $sale->payment_request_id = $response->getRrn();
                $sale->save();
            }
            $clear = \Cart::clear();
            $request->session()->forget('kerala_h_m_ship_option');
            $amount = $response->getTrnAmt()/100;
            //SEND SMS START
            $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
            $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
            $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
            $smsText = config('app.name') . ': Your order ' . $order_no . ' amounting to Rs. ' . $amount . ' placed. We will send you an update when the order is shipped.';
            //SEND SMS END
            //$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');
            $sendSMS = $this->sendsms($mblno, $smsText);

            $customer_id = Auth::guard('customer')->user()->id;
            if($customer_id) {
                $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
                return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails]);
            }else {
                return redirect()->route('index');
            }
        }else {
            $order_no = $response->getOrderId();
            $sale = Sale::where('order_no',$order_no)->latest()->first();
            $customer_id = Auth::guard('customer')->user()->id;
            if($customer_id) {
                $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
                return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails]);
            }else {
                return redirect()->route('index');
            }
        }

    }

    public function payment_webhook(Request $request) {

        $data = $_POST;
        $mac_provided = $data['mac'];  // Get the MAC from the POST data
        unset($data['mac']);  // Remove the MAC key from the data.
        $ver = explode('.', phpversion());
        $major = (int) $ver[0];
        $minor = (int) $ver[1];
        if($major >= 5 and $minor >= 4){
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
        }
        else{
            uksort($data, 'strcasecmp');
        }

        $salt = 'e14b908905e846c1b10d27e16ee945a5';
        $mac_calculated = hash_hmac("sha1", implode("|", $data), $salt);
        if($mac_provided == $mac_calculated){
            if($data['status'] == "Credit"){
                // Payment was successful, mark it as successful in your database.
                // You can acess payment_request_id, purpose etc here.

                $mblno = $data['buyer_phone'];
                $order_no = $data['purpose'];
                $sale = Sale::where('order_no',$order_no)->latest()->first();
                if($sale) {
                    $sale->is_paid = 1;
                    $sale->payment_id = $data['payment_id'];
                    $sale->payment_request_id = $data['payment_request_id'];

                    $sale->save();
                }
                $clear = \Cart::clear();
                $request->session()->forget('kerala_h_m_ship_option');
                $amount = $data['amount'];
                //SEND SMS START
                $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
                $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
                $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
                $smsText = config('app.name') . ': Your order ' . $order_no . ' amounting to Rs. ' . $amount . ' placed. We will send you an update when the order is shipped.';
                //SEND SMS END
                //$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');
                $sendSMS = $this->sendsms($mblno, $smsText);

            }
            else{
                // Payment was unsuccessful, mark it as failed in your database.
                // You can acess payment_request_id, purpose etc here.

            }
        }
        else{
            echo "MAC mismatch";
        }

        return '';
    }

    public function pay_later($sale_id) {

        // return 'failed';
        $sale = Sale::find(decrypt($sale_id));
        // return $sale;
        if(!$sale->is_paid) {
            $customerDetails = CustomerDetail::where('customer_id',$sale->customer->id)->first();

            $amount= $sale->sub_total+$sale->delivery_charge;
            $phone = Auth::guard('customer')->user()->phone;
            $email = Auth::guard('customer')->user()->email;
            $name = $customerDetails->name;
            $marchantId = Utility::marchantId;
            $consumerId = encrypt($sale->customer->id);
            $salt = Utility::FEDSALT;

            return $this->fedPayProcess($marchantId,$sale->order_no,$amount,$consumerId,$name,$phone, $email,$salt);

            // $sendPayment = new InstaMojoController($amount,$purpose,$phone,$email,$name,$redirectUrl);
            // $createRequest = $sendPayment->createRequest();
            // $success_route = route('payment.online',['request_url' => $createRequest]);
            // return response()->json($success_route);
        }else {
            $myorders = route('myorders');
            return response()->json($myorders);
        }

    }

    /*public function sendsms($host,$username,$password,$sender, $message,$mobile,$msgtype,$dlr)
    {
        $sendsms = new SmsApiController($host,$username,$password,$sender, $message,$mobile,$msgtype,$dlr);
        return $sendsms->Submit();
    }*/

    public function sendsms($mobile, $message)
    {
        $sendsms = new SmsFastMsgController($mobile, $message);
        return $sendsms->Submit();
    }

    public function subscribe(Request $request) {
        $subscriber = Subscribe::where('phone',$request->phone)->first();
        if($subscriber) {
            return response()->json(['subscribe' => 0]);
        }else {
            $subscribe = new Subscribe();
            $input = [];
            $input['phone'] = $request->phone;
            $subscribe->fill($input)->save();
            return response()->json(['subscribe' => 1]);
        }
    }

    public  function profile () {
        $customer_id = Auth::guard('customer')->user()->id;
        $customer = Customer::find($customer_id);
        $states = DB::table('states')->select('id','name')->get();
        return view('pages.profile',['customer'=>$customer, 'states' =>$states]);
    }

    public function profile_update (Request $request) {
        /*return $request->name;*/
        $validator = $this->profile_validator($request->all());
        $customer_id = Auth::guard('customer')->user()->id;

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('profile')
                    ->withErrors($validator)
                    ->withInput();
            }
        }else {
            $customer = Customer::find($customer_id);
            $customer->email = $request->email;
            $customer->save();

            $customerDetails = CustomerDetail::where('customer_id',$customer_id)->first();
            $address = [ 'phone_alt' => $request->phone_alt, 'address' => $request->address, 'place' => $request->place, 'city' => $request->city, 'pincode' => $request->pincode, 'state' => $request->state, 'district' => $request->district];

            $customerDetails->fill([
                'name' => $request->name,
                'address' => $address,
                'gstin' => $request->gstin
            ]);
            $customerDetails->save();
        }
        $data['customer'] = $customer;
        $data['customer_details'] = $customerDetails;
        return response()->json(['data'=>$data]);
    }

    protected function profile_validator(array $data)
    {
        $messages = [
            'name.required' => 'Name is Required',
            'email.required' => 'Email is Required',
            'address.required' => 'Address is Required',
            'pincode.required' => 'PO Box is Required',
            'place.required' => 'Place is Required',
            'state.required' => 'State is Required',
            'district.required' => 'District is Required',
        ];
        return Validator::make($data, [
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'pincode' => 'required|digits:6',
            'place' => 'required',
            'state' => 'required',
            'district' => 'required',

        ],$messages);


    }

    public function cancel_order(Request $request) {
        $data=[];
        $customer_id = Auth::guard('customer')->user()->id;
        $sale_id = $request->sale;

        $sale= Sale::find($sale_id);
        if($sale->status==Utility::SALE_STATUS_NEW) {
            foreach ($sale->sale_details as $detail) {
                $stock = Utility::get_stock($detail->product_id, $detail->type_size);
                $new_stock = $stock + $detail->quantity;
                $stock_details = TypeProductPivot::where('type_id', $detail->type_size)->where('product_id', $detail->product_id)->first();
                $stock_details->stock = $new_stock;
                $stock_details->save();
            }
            $sale->status = Utility::SALE_STATUS_CANCELLED;
            $sale->is_cancelled_customer = Utility::SALE_STATUS_CANCELLED_BY_CUST;
            $sale->save();
            $data['canceled'] = 1;
        }else {
            $data['status'] = Utility::saleStatus()[$sale->status];
            $data['canceled'] = 0;
        }

        $sales_count = Sale::where('customer_id',$customer_id)->where('status','!=',Utility::SALE_STATUS_CANCELLED)->get()->count();

        $data['sale_id'] = $sale_id;
        $data['count'] = $sales_count;

        return $data;
    }

    /*public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img('khm')]);
    }*/

    public  function search_on_type (Request $request) {
        $cat_id = $request->cat_id;
        $subcat_id = $request->subcat_id;
        $term = $request->has('term')?$request->term:'';
        $term_display = $request->has('term')?$request->term:'';
        $allproducts = Product::where('name', 'like', '%'.$term.'%')->where('is_active',1);

        if ($request->has('cat_id')&&!empty($cat_id)) {
            $allproducts->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->cat_id);
            });
        }


            $allproducts = $allproducts->get();



        $htmlData= '';

        $produCount = 1;
        foreach($allproducts as $allproduct) {
            if($produCount <=10) {
                if ($allproduct->image != '') {
                    $image_thumb = asset(Utility::DEFAULT_STORAGE . Product::FILE_DIRECTORY .  '/' . $allproduct->image);
                } else {
                    $image_thumb = asset('images/no-image.jpg');
                }

                $all_slug = AllSlug::where('causer_id',$allproduct->id)->where('causer_type', 'App\Models\Product')->first();
                $slug = $all_slug->slug;
                $allproduct->slug = $slug;

                $htmlData .= '<div class="display_box" align="left">
                    <a href="' . route('all.slug', [$slug]) . '">
                        <img src="' . $image_thumb . '" />
                        <span class="name">' . $allproduct->name . '</span><br/>
                    </a>
                </div>';
            }
            $produCount++;
        }

        if(!empty($htmlData)) {
            $htmlData .= '<div class="display_box_footer" align="left">
                                            <span class="name">For more results click search icon</span><br/>
                                        </div>';
        }else {
            $htmlData .= '<div class="display_box_footer" align="left">
                                            <span class="name">No items found</span><br/>
                                        </div>';
        }


        return $htmlData;
    }

    public function all_slug($slug) {
        $all_slug = AllSlug::where('slug',$slug)->first();
        if($all_slug) {
            $causer_type = $all_slug->causer_type;
            $causer_id = $all_slug->causer_id;
            $page_slug = $all_slug->slug;
            if ($causer_type == 'App\Models\Product') {
                $product = Product::where('id', $causer_id)->where('is_active', 1)->first();
                if ($product) {
                    $id = $product->id;
                    $categories = $product->categories;
                    $cat_ids = [];
                    foreach ($categories as $category) {
                        $cat_ids[] = $category->id;
                    }
                    $relatedProducts = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($cat_ids, $id) {
                        $query->whereIn('categories.id', $cat_ids)->whereNotIn('products.id', [$id]);
                    })->latest()->get();

                    foreach ($relatedProducts as $relatedProduct) {
                        $all_slug_r = AllSlug::where('causer_id', $relatedProduct->id)->where('causer_type', 'App\Models\Product')->first();
                        $relatedslug = $all_slug_r->slug;
                        $relatedProduct->slug = $relatedslug;
                    }

                    return view('pages.product_detail', ['product' => $product, 'relatedProducts' => $relatedProducts, 'slug' => $page_slug]);
                } else {
                    abort('404');
                }
            }
            elseif ($causer_type == 'App\Models\Brand') {

                $brand = Brand::findOrFail($causer_id);

                if ($brand) {
                    $products = Product::where('is_active', 1)->whereHas('brands', function ($query) use ($causer_id) {
                        $query->whereIn('brands.id', [$causer_id]);
                    })->latest()->paginate(24);
                    foreach ($products as $product) {
                        $all_slug = AllSlug::where('causer_id', $product->id)->where('causer_type', 'App\Models\Product')->first();
                        $product->slug = $all_slug->slug;
                    }
                    return view('pages.brand_products', ['brand' => $brand, 'products' => $products]);
                } else {
                    return abort(404);
                }

            } elseif ($causer_type == 'App\Models\Category') {

                $mainCategory = Category::findOrFail($causer_id);

                $child_count = $mainCategory->childs->count();
                if($child_count==0) {
                    $products = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($causer_id) {
                        $query->whereIn('categories.id', [$causer_id]);
                    })->latest()->paginate(24);

                    foreach ($products as $product) {
                        $all_slug = AllSlug::where('causer_id', $product->id)->where('causer_type', 'App\Models\Product')->first();
                        $product->slug = $all_slug->slug;
                    }
                    return view('pages.products', ['category' => $mainCategory, 'products' => $products]);
                }else {
                    $child_product_array=[];
                    $child_product_id_array=[];
                    $child_cat_ids=[];
                    foreach($mainCategory->childs as $child_category) {
                        $child_cat_ids[] = $child_category->id;
                    }

                    $child_products = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($child_cat_ids) {
                        $query->whereIn('categories.id', $child_cat_ids);
                    })->get();

                    foreach ($child_products as $child_product) {
                        if (!in_array($child_product->id, $child_product_id_array)) {
                            array_push($child_product_array, $child_product);
                            $child_product_id_array[] = $child_product->id;
                            $all_slug = AllSlug::where('causer_id',$child_product->id)->where('causer_type', 'App\Models\Product')->first();
                            $slug_al = $all_slug->slug;
                            $child_product->slug = $slug_al;
                            $child_product->price = $child_product->min_price();
                            $child_product->min_mrp = $child_product->min_mrp();
                        }
                    }

                    return view('pages.subcategories',['category' => $mainCategory,'products'=>$child_product_array]);
                }

            } elseif ($causer_type == 'App\Models\Affiliate') {
                $affiliate = Affiliate::findOrFail($causer_id);

                if ($affiliate) {
                    $banners = Banner::where('is_active',1)->where(function($query) {
                        $query->where('user_id',Utility::KHM_USER_ID);
                    })->orderBy('order_no','asc')->get();

                    $offerCatId = Utility::CATEGORY_ID_OFFER;
                    $offerProudcts = Product::where('is_active',1)->whereHas('categories', function($query) use($offerCatId) {
                        $query->where('categories.id', $offerCatId);
                    })->take(8)->latest()->get();

                    foreach($offerProudcts as $offerProudct) {
                        $slug_offer = AllSlug::where('causer_id',$offerProudct->id)->where('causer_type', 'App\Models\Product')->first();
                        $slug_ofr = $slug_offer->slug;
                        $offerProudct->slug = $slug_ofr;
                    }

                    $mainCategories = Category::where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')->get();

                    foreach($mainCategories as $mainCategory) {
                        foreach($mainCategory->products as $allProduct) {
                            $all_slug = AllSlug::where('causer_id',$allProduct->id)->where('causer_type', 'App\Models\Product')->first();
                            $slug_al = $all_slug->slug;
                            $allProduct->slug = $slug_al;
                        }
                        $all_slug_mainCategory = AllSlug::where('causer_id',$mainCategory->id)->where('causer_type', 'App\Models\Category')->first();
                        $mainCategory->slug = $all_slug_mainCategory->slug;
                    }

                    $featuredProudcts = Product::where('is_active',1)->where('is_featured',1)->take(8)->latest()->get();
                    foreach($featuredProudcts as $featuredProudct) {
                        $slug_featured = AllSlug::where('causer_id',$featuredProudct->id)->where('causer_type', 'App\Models\Product')->first();
                        $slug = $slug_featured->slug;
                        $featuredProudct->slug = $slug;
                    }

                    $affiliate_sess = $this->getAffiliate($causer_id);
                    return view('affiliates.home', ['banners' => $banners, 'offerProudcts' => $offerProudcts, /*'featuredProudcts'=>$featuredProudcts,*/
                        'mainCategories' => $mainCategories, 'slug' => $page_slug]);
                }
            } else {
                return abort(404);
            }
        }else {
            return abort(404);
        }
    }

    public function store_products($slug,$id) {
        $all_slug = AllSlug::where('slug',$slug)->first();
        $causer_type = $all_slug->causer_type;
        $causer_id = $all_slug->causer_id;
        $page_slug = $all_slug->slug;
        if($causer_type=='App\Models\Store') {
            $store = Store::where('id',$causer_id)->where('is_active',1)->first();
            if($store) {
                $store->username = $page_slug;

                $category = Category::findOrFail($id);

                $products = Product::where('is_active',1)->whereHas('categories', function($query) use($id) {
                    $query->whereIn('categories.id', [$id]);
                })->latest()->paginate(24);

                foreach($products as $product) {
                    $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
                    $product->slug = $all_slug->slug;
                }

                return view('pages.store_products', ['store' => $store, 'products'=>$products, 'category'=>$category->name]);
            }else {
                return abort(404);
            }
        }else {
            return abort(404);
        }
    }

    // public function getdoctors(Request $request,$slug) {
    //     $get_slug = AllSlug::where('slug',$slug)->first();
    //     $clinic_id = $get_slug->causer_id;

    //     $clinic = Clinic::find($clinic_id);
    //     $clinic_phone = !empty($clinic->phone) ? $clinic->phone : '';

    //     $doctor_id = $request->doctor_id;
    //     $doctor = Doctor::findOrFail($doctor_id);

    //     $doctor_timing = DB::table('clinic_doctor')->where('doctor_id',$doctor_id)->where('clinic_id',$clinic_id)->first();
    //     $doctor->avail_time = $doctor_timing ? $doctor_timing->avail_time : '';

    //     $treatment_default = DB::table('doctor_treatment')->where('doctor_id',$doctor_id)->orderBy('treatment_id','asc')->limit(1)->get();
    //     $treatment_default_id = !empty(json_decode($treatment_default)) ? $treatment_default[0]->treatment_id : '';
    //     $returnHTML = view('includes.doctors',['doctor' => $doctor, 'clinic_phone' => $clinic_phone])->render();

    //     return response()->json(['content'=>$returnHTML,'treatment_default'=>$treatment_default_id]);
    // }

    // public function gettreatmentimg(Request $request,$slug) {
    //     $treatment_id = $request->treatment_id;
    //     $treatment = Treatment::findOrFail($treatment_id);

    //     $returnHTML = view('includes.treatments',['treatment' => $treatment])->render();

    //     return response()->json($returnHTML);
    // }

    public function getAffiliate($id) {
        session(['kerala_h_m_affiliate' => $id]);
        return session('kerala_h_m_affiliate');
    }

    // public  function clinics() {

    //     $sliders = Slider::where('is_active',1)->where('type',Utility::SLIDER_TYPE_CLINIC)->orderBy('order_no','asc')->get();
    //     $clinics = Clinic::where('is_active',1)->latest()->paginate(18);
    //     foreach($clinics as $clinic) {
    //         $all_slug = AllSlug::where('causer_id',$clinic->id)->where('causer_type', 'App\Models\Clinic')->first();
    //         $slug = $all_slug->slug;
    //         $clinic->slug = $slug;
    //     }
    //     return view('pages.services',['clinics' => $clinics,'sliders' => $sliders]);
    // }


    // public  function clinic_search (Request $request) {
    //     $type_id = $request->type_id;
    //     $district_id = $request->district_id;
    //     $term = $request->term;
    //     $term_display = $request->has('term')?$request->term:'';

    //     $allclinics = Clinic::where('is_active',1);

    //     if ($request->has('type_id')) {
    //         $allclinics->where('type',$type_id);
    //     }

    //     if ($request->has('district_id')) {
    //         $allclinics->where('district',$district_id);
    //     }

    //     if ($request->has('term')) {
    //         $city = City::where('name',$term)->first();
    //         if($city) {
    //             $allclinics->where('city',$city->id);
    //         }else {
    //             $allclinics->where('city',0);
    //         }
    //     }

    //     $allclinics = $allclinics->get();

    //     foreach($allclinics as $clinic) {
    //         $all_slug = AllSlug::where('causer_id',$clinic->id)->where('causer_type', 'App\Models\Clinic')->first();
    //         $slug = $all_slug->slug;
    //         $clinic->slug = $slug;
    //     }

    //     return view('pages.services',['clinics' => $allclinics, 'term'=>$term_display, 'selected_type' => $type_id, 'selected_district' => $district_id]);
    // }

    public  function test_pay() {
        return view('pages.test_pay');
    }
    public  function meTrnPay($order_id,$amount_paisa,$name,$mobile)
    {
        //create an Object of the above included class
        $obj = new AWLMEAPI();

        //create an object of Request Message
        $reqMsgDTO = new ReqMsgDTO();

        /* Populate the above DTO Object On the Basis Of The Received Values */
        // PG MID
        $reqMsgDTO->setMid(Utility::FED_MID);
        // Merchant Unique order id
        $reqMsgDTO->setOrderId($order_id);
        //Transaction amount in paisa format
        $reqMsgDTO->setTrnAmt($amount_paisa);
        //Transaction remarks
        $reqMsgDTO->setTrnRemarks("This txn has to be done ");
        // Merchant transaction type (S/P/R)
        $reqMsgDTO->setMeTransReqType(Utility::FED_METRANSREQTYPE);
        // Merchant encryption key
        $reqMsgDTO->setEnckey(Utility::FED_ENCRYPTION_KEY);
        // Merchant transaction currency
        $reqMsgDTO->setTrnCurrency(Utility::FED_CURRENCY);
        // Recurring period, if merchant transaction type is R
        $reqMsgDTO->setRecurrPeriod('NA');
        // Recurring day, if merchant transaction type is R
        $reqMsgDTO->setRecurrDay('');
        // No of recurring, if merchant transaction type is R
        $reqMsgDTO->setNoOfRecurring('');
        // Merchant response URl
        $reqMsgDTO->setResponseUrl(route('checkout.payment.success'));
        // Optional additional fields for merchant
        $reqMsgDTO->setAddField1($mobile);
        $reqMsgDTO->setAddField2($name);
        $reqMsgDTO->setAddField3('');
        $reqMsgDTO->setAddField4('');
        $reqMsgDTO->setAddField5('');
        $reqMsgDTO->setAddField6('');
        $reqMsgDTO->setAddField7('');
        $reqMsgDTO->setAddField8('');

        /*
         * After Making Request Message Send It To Generate Request
         * The variable `$urlParameter` contains encrypted request message
         */
        //Generate transaction request message
        $merchantRequest = "";

        $reqMsgDTO = $obj->generateTrnReqMsg($reqMsgDTO);

        if ($reqMsgDTO->getStatusDesc() == "Success"){
            $merchantRequest = $reqMsgDTO->getReqMsg();
            $getMid = $reqMsgDTO->getMid();
            return view('pages.meTrnPay',['merchantRequest'=>$merchantRequest, 'getMid' => $getMid]);
        }
    }

    public  function meTrnSuccess()
    {
        //create an Object of the above included class
        $obj = new AWLMEAPI();

        /* This is the response Object */
        //$resMsgDTO = new ResMsgDTO();

        /* This is the request Object */
        //$reqMsgDTO = new ReqMsgDTO();

        //This is the Merchant Key that is used for decryption also
        $enc_key = "6375b97b954b37f956966977e5753ee6";

        /* Get the Response from the WorldLine */
        $responseMerchant = $_REQUEST['merchantResponse'];

        $response = $obj->parseTrnResMsg( $responseMerchant , $enc_key );

        return view('pages.test_pay_success',['response'=>$response]);
    }

    public  function test()
    {
        // //SEND SMS START
        // $smsText = config('app.name') . ': Your order 452145 amounting to Rs. 458 placed. We will send you an update when the order is shipped.';
        // $mblno = Setting::IND_CODE . '9809373738';
        // //SEND SMS END

        // if(!empty($mblno)) {
        //     $sendSMS = $this->sendsms($mblno, $smsText);
        // }

        $link = 'https://www.youtube.com/shorts/sqhs16SE-r8';
        //$text = explode('watch?v=', 'https://www.youtube.com/watch?v=Ec3mAbp6YZI', 2)[1];
        // return $text;

        if (str_contains($link, 'watch?v=')) {
            $text = explode('watch?v=', $link, 2)[1];
        }
        elseif (str_contains($link, 'shorts/')) {
            $text = explode('shorts/', $link, 2)[1];
        }
        else {
            $text = 'Unsupported Video link format';
        }
        // return $text;

        // return Utility::cleanString('646_Elbow_Support_7”_Lycra_Single');



        // function isPrime($x) {
        //     $prime = true;
        //     switch ($x) {
        //         case $x<=1:
        //             $prime = false;
        //           break;
        //         case $x==2|| $x==3||$x==5||$x==7:
        //           break;
        //         default:
        //         if(($x%2==0)|| $x%3==0||$x%5==0||$x%7==0) {
        //             $prime = false;
        //         }
        //       }
        //       return $prime ?'Prime Number':'Not a Prime Number';
        // }
        // // echo isPrime(2);


        // echo Carbon::parse('05-02-2024 14:22:57')->format('Y-m-d H:i:s');










    }


}
