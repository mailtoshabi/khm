<?php

namespace App\Http\Controllers\Admin;

use App\Models\Affiliate;
use App\Models\AllSlug;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OneclickPurchase;
use App\Models\Prescription;
use App\Models\PriceDetail;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Sale;
use App\Models\Services\Slug;
use App\Models\TypeProductPivot;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Uuid;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Utilities\Utility;

class AffiliateSelfController extends Controller
{
    public  function index() {

        /*$user = User::find(Auth::id());
        $affiliate_id = $user->affiliate->id;
        $products = Product::join('category_product', 'products.id', '=', 'category_product.product_id')
            ->join('affiliate_category', function ($join) use($affiliate_id) {
                $join->on('affiliate_category.category_id', '=', 'category_product.category_id')
                    ->where('affiliate_category.affiliate_id', $affiliate_id);
            })
            ->select('products.*')
            ->get();

        return $products;*/
        return view('admin.pages.affiliates.products');
    }
    public  function data() {
        $user = User::find(Auth::id());
        $affiliate_id = $user->affiliate->id;
        /*$affiliate = Affiliate::findOrFail($affiliate_id);
        $mainCategories = $affiliate->categories;

        $products = [];
        $product_id_array=[];
        foreach($mainCategories as $mainCategory) {
            foreach($mainCategory->products as $product) {
                if (!in_array($product->id, $product_id_array)) {
                    $product_id_array[] = $product->id;
                    array_push($products, $product);
                }
            }
        }*/


        $products = Product::where('is_active', 1)
            ->join('category_product', 'products.id', '=', 'category_product.product_id')
            ->join('affiliate_category', function ($join) use($affiliate_id) {
                $join->on('affiliate_category.category_id', '=', 'category_product.category_id')
                    ->where('affiliate_category.affiliate_id', $affiliate_id);
            })
            ->join('brand_product', 'products.id', '=', 'brand_product.product_id')
            ->join('affiliate_brand', function ($join) use($affiliate_id) {
                $join->on('affiliate_brand.brand_id', '=', 'brand_product.brand_id')
                    ->where('affiliate_brand.affiliate_id', $affiliate_id);
            })
            /*->join('affiliate_product', function ($join) use ($affiliate_id) {
                $join->on('products.id', '=', 'affiliate_product.product_id')
                    ->where('affiliate_product.affiliate_id', '=', $affiliate_id)
                    ->where('affiliate_product.is_home', '=', 1);
            })*/
            ->select('products.*')
            ->get();


        return DataTables::of($products)
            /*return Datatables::eloquent(Product::select())*/
            ->rawColumns(['name','categories','action'])
            ->editColumn('name', function ($modal) {
                $main_image = !empty($modal->image) ? '<img src="' . asset(Utility::DEFAULT_STORAGE . Product::FILE_DIRECTORY . '/'. $modal->image) . '" alt="" height="50" />' : '';

                return '<p>' . $modal->name . " " . $main_image . '</p>' . '<br> Brand : ' . $modal->brand()->name;
            })
            ->editColumn('categories', function ($modal) {
                $categories = $modal->categories->pluck('name');
                $show = '<ul>';
                foreach($categories as $category) {
                    $show .=  '<li>' . $category . '</li>';
                }
                $show .= '</ul>';
                return $show;
            })
            ->addColumn('action', function ($modal) {
                $have_product = $modal->affiliates()->where('user_id',Auth::id())->first();
                $publishIcon = $have_product ? 'fa fa-pencil' : 'fa fa-plus';
                $publishTitle = $have_product ? 'Edit Product' : 'Add Product';
                $data = '<a  href="'. route('admin.affiliate.products.edit',[$modal->id]) . '" title="' . $publishTitle . '" > <i class="' . $publishIcon . ' text-primary"></i></a>';
                /*$data = '<a data-action="' . route('admin.affiliate.products.add_product',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="" data-type="GET" title="' . $publishTitle . '"> <i class="' . $publishIcon . ' text-primary"></i></a>';*/
                if($have_product) {
                    $data .= '&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.affiliate.products.remove_product',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Doy really need to remove the product from your list." data-type="GET" title="Remove Product"> <i class="fa fa-remove text-primary"></i></a>';
                }
                return $data;
            })
            ->setRowId('product_{{$id}}')
            ->make(true);
    }
    //admin.affiliate.products.add_product
    public function edit($id) {
        $product = Product::findOrFail($id);
        $user = User::find(Auth::id());
        $affiliate_id = $user->affiliate->id;
        $dealers = $product->brand()->dealers;
        foreach($dealers as $dealer) {
            $af_id = $dealer->affiliate->id;
            $dp = DB::table('affiliate_product')->where(['affiliate_id' => $af_id, 'product_id' => $id])->first();
            $dealer->dp = isset($dp) ? $dp->distributor_price : '-';
            //$dealer->dp = '-';
        }
        $brand_id = $product->brand()->id;
        $is_dealer = DB::table('brand_user')->where(['brand_id' => $brand_id, 'user_id' => Auth::id()])->first();

        $type_sizes = TypeProductPivot::where('product_id',$id)->distinct('type_id')->get();
        foreach($type_sizes as $key=>$type_size) {
            $affiliate_product_type = DB::table('affiliate_product_type')->where(['affiliate_id' => $affiliate_id, 'product_id' => $id, 'type_id' => $type_size->type_id])->first();
                $type_size->profit = empty($affiliate_product_type) ? '' : $affiliate_product_type->profit;
                $type_size->profit_type = empty($affiliate_product_type) ? '' : $affiliate_product_type->profit_type;
        }

        $affiliate_product = DB::table('affiliate_product')->where(['affiliate_id' => $affiliate_id, 'product_id' => $id])->first();


        return view('admin.pages.affiliates.add-details',['product'=>$product, 'type_sizes'=>$type_sizes,'affiliate_product'=>$affiliate_product, 'dealers'=>$dealers, 'is_dealer'=>$is_dealer]);
    }

    public function update(Request $request, $id) {
        $rules = [
           /* 'profit' => 'required',*/
        ];
        $messages = [
            /*'required' => 'The :attribute field is required.',*/
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.affiliate.products.edit',$id)
                    ->withErrors($validator)
                    ->withInput();
            }

        }
        else {
            $product = Product::find($id);
            $have_product = $product->affiliates->where('user_id',Auth::id())->first();
            $user = User::find(Auth::id());

            $affiliate_id = $user->affiliate->id;

            if($have_product) {
                //
            }else{
                $product->affiliates()->attach($affiliate_id);
            }

            foreach($request->profit as $index => $profit) {
                if(!empty($profit)) {

                    $affiliate_product_type = DB::table('affiliate_product_type')
                        ->where(['affiliate_id' => $affiliate_id, 'product_id' => $id, 'type_id' => $request->type_size[$index]])
                        ->get();

                    if(!empty(json_decode($affiliate_product_type))) {
                        DB::table('affiliate_product_type')
                            ->where(['affiliate_id' => $affiliate_id, 'type_id' => $request->type_size[$index], 'product_id' => $id])
                            ->update(
                                ['profit' => $profit,'profit_type' => Utility::PROFIT_TYPE_SELLINGRATE]
                            );
                    }else {
                        DB::table('affiliate_product_type')
                            ->insert(
                                ['affiliate_id' => $affiliate_id, 'type_id' => $request->type_size[$index], 'product_id' => $id, 'profit' => $profit,'profit_type' => Utility::PROFIT_TYPE_SELLINGRATE]
                            );
                    }
                }
            }

            /*$affiliate_product = DB::table('affiliate_product')
                ->where(['affiliate_id' => $affiliate_id, 'product_id' => $id])
                ->get();


            if(!empty(json_decode($affiliate_product))) {
                DB::table('affiliate_product')
                    ->where(['affiliate_id' => $affiliate_id, 'product_id' => $id])
                    ->update(
                        ['site_title' => $request->site_title, 'site_keywords' => $request->site_keywords, 'site_description' => $request->site_description]
                    );
            }else {
                DB::table('affiliate_product')
                    ->insert(
                        ['affiliate_id' => $affiliate_id, 'product_id' => $id, 'site_title' => $request->site_title, 'site_keywords' => $request->site_keywords, 'site_description' => $request->site_description]
                    );
            }*/

            $is_home = $request->is_home == '' ? 0 : $request->is_home;
            $is_offer = $request->is_offer == '' ? 0 : $request->is_offer;

            DB::table('affiliate_product')
                ->where(['affiliate_id' => $affiliate_id, 'product_id' => $id])
                ->update(
                    ['is_home' => $is_home,'is_offer' => $is_offer,'distributor_price' => $request->distributor_price,'site_title' => $request->site_title, 'site_keywords' => $request->site_keywords, 'site_description' => $request->site_description]
                );

            if ($request->ajax()) {
                return response()->json(['success' => 'Product Details has been updated successfully']);
            } else {
                return redirect()->route('admin.affiliate.products.index')->with('success', 'Product Details has been updated successfully');
            }
        }
    }

    /*public function add_product($id, Request $request) {

        $product = Product::find($id);

        $have_product = $product->affiliates->where('user_id',Auth::id())->first();

        $user = User::find(Auth::id());
        $affiliate_id = $user->affiliate->id;

        if($have_product) {
            $msg = 'Product is being redirected to edit';
            $target = 1;
        }else{
            $product->affiliates()->attach($affiliate_id);
            $msg = 'Product has been added successfully.';
            $target = 1;
        }
        $success_route = route('admin.affiliate.products.edit',$product->id);

        if ($request->ajax()) {
            return response()->json(['target' => $target,'success_route' => $success_route]);
        } else {
            return redirect()->route('admin.affiliate.products.index')->with('success', $msg);
        }
    }*/

    public function remove_product($id) {
        $product = Product::find($id);

        $have_product = $product->affiliates->where('user_id',Auth::id())->first();

        $user = User::find(Auth::id());
        $affiliate_id = $user->affiliate->id;

        if($have_product) {
            $product->affiliates()->detach($affiliate_id);

            $affiliate_product_types = DB::table('affiliate_product_type')
                ->where(['affiliate_id' => $affiliate_id, 'product_id' => $id])
                ->delete();
            /*foreach($affiliate_product_types as $affiliate_product_type) {
                $affiliate_product_type->delete();
            }*/
        }
        $target = 0;
        return response()->json(['success' => 'Product has been Removed from your list successfully', 'target' => $target]);
    }

    public function getprice(Request $request) {
        return Utility::getParticularAffiliatePrice($request->profit, $request->product_id, $request->type, $request->profit_type);
    }


    public  function category_index() {
        return view('admin.pages.affiliates.categories');
    }

    public  function category_data() {

        return Datatables::eloquent(Category::select()->where('is_active',1)) //has('parents',0)->->oldest()
        ->rawColumns(['name','action'])
            ->editColumn('name', function ($modal) {
                $data = '<p>' . $modal->name . '</p>';
                // $child_names = [];
                // if(!empty($modal->childs)) {
                //     foreach($modal->childs as $child) {
                //         $child_names[] = $child->name;
                //     }
                // }
                // if(!empty($child_names)) {
                //     $data .= '<p class="text-primary">Child(s) : <small>' . implode(", ",$child_names) . '</small></p>';
                // }
                return $data;
            })
            ->addColumn('action', function ($modal) {
                $have_product = $modal->affiliates->where('user_id',Auth::id())->first();
                $publishIcon = $have_product ? 'fa fa-check' : 'fa fa-plus';
                $publishTitle = $have_product ? 'Remove Category' : 'Add Category';
                $confMessage = $have_product ? 'Do you really want to Remove Category and its related data' : 'Do you really want to Add Category';
                return'<a data-action="' . route('admin.affiliate.categories.add_category',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="' . $confMessage . '" data-type="GET" title="' . $publishTitle . '"> <i class="' . $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('category_{{$id}}')
            ->make(true);
    }

    // public  function subcategory_index() {
    //     return view('admin.pages.affiliates.subcategories');
    // }

    // public  function subcategory_data() {
    //     $user = User::find(Auth::id());
    //     $affiliate_id = $user->affiliate->id;
    //     $affiliate = Affiliate::findOrFail($affiliate_id);
    //     $mainCategories = $affiliate->main_categories;


    //     $childs = [];
    //     $child_id_array=[];
    //     foreach($mainCategories as $mainCategory) {
    //         foreach($mainCategory->childs as $child) {
    //             if (!in_array($child->id, $child_id_array)) {
    //                 $child_id_array[] = $child->id;
    //                 array_push($childs, $child);
    //             }
    //         }
    //     }

    //     return DataTables::of($childs)
    //     ->rawColumns(['name','parent','action'])
    //         ->editColumn('name', function ($modal) {
    //             $data = '<p>' . $modal->name . '</p>';
    //             $child_names = [];
    //             if(!empty($modal->childs)) {
    //                 foreach($modal->childs as $child) {
    //                     $child_names[] = $child->name;
    //                 }
    //             }
    //             if(!empty($child_names)) {
    //                 $data .= '<p class="text-primary">Child(s) : <small>' . implode(", ",$child_names) . '</small></p>';
    //             }
    //             return $data;
    //         })
    //         ->addColumn('parent', function ($modal) {
    //             $parent_names = [];
    //             if(!empty($modal->parents)) {
    //                 foreach($modal->parents as $parent) {
    //                     $parent_names[] = $parent->name;
    //                 }
    //             }
    //             return implode(", ",$parent_names);
    //         })
    //         ->addColumn('action', function ($modal) {
    //             $have_product = $modal->affiliates->where('user_id',Auth::id())->first();
    //             $publishIcon = $have_product ? 'fa fa-check' : 'fa fa-plus';
    //             $publishTitle = $have_product ? 'Remove Category' : 'Add Category';
    //             $confMessage = $have_product ? 'Do you really want to Remove Category and its related data' : 'Do you really want to Add Category';
    //             return'<a data-action="' . route('admin.affiliate.categories.add_category',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="' . $confMessage . '" data-type="GET" title="' . $publishTitle . '"> <i class="' . $publishIcon . ' text-primary"></i></a>';
    //         })
    //         ->setRowId('category_{{$id}}')
    //         ->make(true);
    // }

    public function add_category($id, Request $request) {

        $category = Category::find($id);
        $have_cat = $category->affiliates->where('user_id',Auth::id())->first();

        $user = User::find(Auth::id());
        $affiliate = $user->affiliate;

        if($have_cat) {
            $status = 'removed';
            // if(!empty($category->childs)) {
            //     foreach($category->childs as $child) {
            //         $affiliate_child = DB::table('affiliate_category')->where('affiliate_id', $affiliate->id)->where('category_id', $child->id)->first();
            //         if($affiliate_child) {
            //             foreach ($child->products as $child_product) {
            //                 DB::table('affiliate_product')->where('affiliate_id', $affiliate->id)->where('product_id', $child_product->id)->delete();
            //                 DB::table('affiliate_product_type')->where('affiliate_id', $affiliate->id)->where('product_id', $child_product->id)->delete();
            //             }
            //             /*$affiliate->categories()->detach($child->$id);*/
            //             DB::table('affiliate_category')->where('affiliate_id', $affiliate->id)->where('category_id', $child->id)->delete();
            //         }
            //     }
            // }
            foreach ($category->products as $product) {
                DB::table('affiliate_product')->where('affiliate_id', $affiliate->id)->where('product_id', $product->id)->delete();
                DB::table('affiliate_product_type')->where('affiliate_id', $affiliate->id)->where('product_id', $product->id)->delete();
            }
            $affiliate->categories()->detach($id);

        }else{
            $affiliate->categories()->attach($id);
            $status = 'added';
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Category has been ' . $status . ' to successfully']);
        } else {
            return redirect()->route('admin.affiliate.categories.index')->with('success', 'Category has been ' . $status . ' to successfully');
        }
    }

    public  function brand_index() {
        return view('admin.pages.affiliates.brands');
    }

    public  function brand_data() {

        return Datatables::eloquent(Brand::select()->where('is_active',1))
            ->rawColumns(['name','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $have_brand = $modal->affiliates->where('user_id',Auth::id())->first();
                $publishIcon = $have_brand ? 'fa fa-check' : 'fa fa-plus';
                $publishTitle = $have_brand ? 'Remove Brand' : 'Add Brand';
                return '<a  href="'. route('admin.affiliate.brands.add_brand',[$modal->id]) . '" title="' . $publishTitle . '" > <i class="' . $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('brand_{{$id}}')
            ->make(true);
    }

    public function add_brand($id, Request $request) {

        $brand = Brand::find($id);
        $have_brand = $brand->affiliates->where('user_id',Auth::id())->first();

        $user = User::find(Auth::id());
        $affiliate = $user->affiliate;

        if($have_brand) {
            $affiliate->brands()->detach($id);
            $status = 'removed';
        }else{
            $affiliate->brands()->attach($id);
            $status = 'added';
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Brand has been ' . $status . ' to successfully']);
        } else {
            return redirect()->route('admin.affiliate.brands.index')->with('success', 'Brand has been ' . $status . ' to successfully');
        }
    }

    public  function banner_index() {
        return view('admin.pages.affiliates.banners');
    }
    public  function banner_data() {

        return Datatables::eloquent(Banner::select()->where(function ($query) {
            $query->where('user_id',Utility::KHM_USER_ID)
                ->orWhere('user_id',Auth::id());
        })->where('is_active',1)->latest())
            ->rawColumns(['link','image','action'])
            ->editColumn('link', function ($modal) {
                $user = $modal->user_id == Utility::KHM_USER_ID ? 'Admin' : 'User';
                $data = !empty($modal->link) ? '<p>' . $modal->link . '</p>' : '--';
                return $data;
            })
            ->editColumn('image', function ($modal) {
                return '<img class="tbl-banner-image" src="' . asset(Utility::DEFAULT_STORAGE . Banner::FILE_DIRECTORY . '/'. $modal->image) . '">' ;
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active_cust==1 ? 'fa-remove' : 'fa-plus';
                $publishTitle = $modal->is_active_cust==1 ? 'Unpublish' : 'Publish';
                return '<a data-action="' . route('admin.affiliate.banners.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active_cust . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('banner_{{$id}}')
            ->make(true);
    }

    public function banner_change_status(Request $request, $id)
    {

        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Banner::find($id);
        if($model) {
            $model->is_active_cust = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public function show_meta() {
        $id = Auth::id();
        $user = User::findOrFail($id);
        return view('admin.pages.affiliates.add-meta',['user'=>$user]);
    }

    public function update_meta(Request $request) {
        $id = Auth::id();
        $affiliate = Affiliate::where('user_id', $id)->first();

        $input = $request->only(['site_title','site_keywords','site_description','upi_id','g_pay','bank_account','footer_description']);
        $affiliate->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Meta Details have been added successfully']);
        } else {
            return redirect()->route('admin.affiliate.show.meta')->with('success', 'Meta Details have been added successfully');
        }
    }

    public function show_oneclick() {
        return view('admin.pages.affiliates.oneclick');
    }

    public  function oneclick_data() {

        return Datatables::eloquent(OneclickPurchase::select()->where('user_id',Auth::id())->orderBy('is_active','desc')->latest())
            ->rawColumns(['phone','product_id','is_active','created_at','action'])
            ->editColumn('created_at', function ($modal) {
                $data = '<p>' . $modal->created_at->format('d-m-Y') . '</p>';
                return $data;
            })
            ->editColumn('phone', function ($modal) {
                $data = '<p>' . $modal->phone . '</p>';
                return $data;
            })
            ->editColumn('product_id', function ($modal) {
                $data = '<p>' . $modal->product->name . '</p>';
                return $data;
            })
            ->editColumn('is_active', function ($modal) {
                return $modal->is_active==1 ? 'New' : 'Closed';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-minus' : 'fa-check';
                $publishTitle = $modal->is_active==1 ? 'New Sale' : 'Sale Closed';
                return '<a data-action="' . route('admin.affiliate.oneclick_purchase.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('oneclick_{{$id}}')
            ->make(true);
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = OneclickPurchase::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }






    // public function show_prescription() {
    //     return view('admin.pages.affiliates.prescription');
    // }

    // public  function prescription_data() {

    //     return Datatables::eloquent(Prescription::select()->where('user_id',Auth::id())->orderBy('is_active','desc')->latest())
    //         ->rawColumns(['phone_prescription','image_prescription','is_active','created_at','action'])
    //         ->editColumn('created_at', function ($modal) {
    //             $data = '<p>' . $modal->created_at->format('d-m-Y') . '</p>';
    //             return $data;
    //         })
    //         ->editColumn('phone_prescription', function ($modal) {
    //             $data = '<p>' . $modal->phone_prescription . '</p>';
    //             return $data;
    //         })
    //         ->editColumn('image_prescription', function ($modal) {
    //             $main_image = !empty($modal->image_prescription) ? '<img src="' . asset($modal->image_prescription) . '" alt="" height="50" />' : '';
    //             $link = !empty($modal->image_prescription) ? '<a target="_blank" href="' . asset($modal->image_prescription) . '" >View</a>' : '';
    //             return '<p>' . $main_image . '</p><p> ' . $link . '</p>';
    //         })
    //         ->editColumn('is_active', function ($modal) {
    //             return $modal->is_active==1 ? 'New' : 'Closed';
    //         })
    //         ->addColumn('action', function ($modal) {
    //             $publishIcon = $modal->is_active==1 ? 'fa-minus' : 'fa-check';
    //             $publishTitle = $modal->is_active==1 ? 'New' : 'Closed';
    //             return '<a data-action="' . route('admin.affiliate.prescriptions.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
    //         })
    //         ->setRowId('prescription_{{$id}}')
    //         ->make(true);
    // }



    // public function change_status_prescription(Request $request, $id)
    // {
    //     $changeStatus = $request->value == 1 ? 0 : 1;
    //     $new_status = $request->value == 1 ? 'inactive' : 'active';
    //     $model = OneclickPurchase::find($id);
    //     if($model) {
    //         $model->is_active = $changeStatus;
    //         $model->save();
    //     }
    //     return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    // }





    public  function show_sale() {

        return view('admin.pages.affiliates.sales');
    }

    public  function sale_data(Request $request) {
        $sales = Sale::select(['id','order_no','pay_method','sub_total','is_paid','delivery_charge','delivery_type','status','is_cancelled_customer','utr_no','is_utr_cust','user_id','created_at'])->where('user_id',Auth::id())->latest();
        return DataTables::of($sales)
            /*return Datatables::eloquent(Sale::select())*/
            ->filter(function ($query) use ($request) {
                if ($request->has('status') && !empty($request->status)) {
                    $query->where('status', $request->status);
                }
            })
            ->rawColumns(['order_no','pay_method','sub_total','status','payment','action'])
            ->editColumn('order_no', function ($modal) {
                $color = $modal->user->id == Utility::KHM_USER_ID ? '' : 'color: red';
                $href = $modal->user->id == Utility::KHM_USER_ID ? '#' : route('admin.affiliates.edit',$modal->user->id);
                $target = $modal->user->id == Utility::KHM_USER_ID ? '_self' : '_blank';
                $admin = $modal->user->id == Utility::KHM_USER_ID ? '(WEB ADMIN' : '';

                $data = '<p>' . $modal->created_at->format('d F, Y') . '</p>';
                $data .= '<p><strong>' . $modal->order_no . '</strong></p>';
                return $data;
            })
            ->editColumn('sub_total', function ($modal) {
                $total = $modal->sub_total+$modal->delivery_charge;
                $data = '<p>' . $total . '</p>';
                if($modal->delivery_charge!=0) {
                    $data .= '<p> <small>including delivery charge</small> Rs.' . $modal->delivery_charge . '</p>';
                }
                return $data;
                /*return '<p>' . $modal->sub_total . '</p>';*/
            })
            ->editColumn('status', function ($modal) {
                $data = '';
                if(($modal->pay_method == Utility::PAYMENT_OFFLINE) && (!empty($modal->utr_no)) && ($modal->is_utr_cust)) {
                    $data .= '<small class="label bg-green">UTR Updated By customer</small>';
                }
                if($modal->status == Utility::SALE_STATUS_CANCELLED) {
                    $post =  $modal->is_cancelled_customer ? 'By Customer' : 'By Admin';
                }else {
                    $post = '';
                }
                $data .= '<p>' . Utility::saleStatus()[$modal->status]. '' . $post . '</p>';
                return $data;
            })
            ->addColumn('payment', function ($modal) {
                $payment = $modal->is_paid ? '<small class="label bg-green">Paid</small>' : '<small class="label bg-red">Not Paid</small>';
                /*$method = $modal->pay_method == Utility::PAYMENT_COD ? '<small class="label label-primary">Cash on Delivery</small>' : '<small class="label label-primary">Online Payment</small>';*/
                if($modal->pay_method == Utility::PAYMENT_COD) {
                    $method = '<small class="label label-primary">Cash on Delivery</small>';
                }elseif($modal->pay_method == Utility::PAYMENT_ONLINE) {
                    $method = '<small class="label label-primary">Online Payment</small>';
                }elseif($modal->pay_method == Utility::PAYMENT_OFFLINE) {
                    $method = '<small class="label label-primary">Offline Payment</small>';
                }else {
                    $method = '<small class="label label-primary">Cash on Delivery</small>';
                }
                if($modal->delivery_type) {
                    $delivery_type = '<p><small class="label label-warning">To Pay Shipping</small></p>';
                }else {
                    $delivery_type = '<p><small class="label label-warning">Paid Shipping</small></p>';
                }

                return '<p>' . $payment . '</p>'.'<p>' . $method . '</p>' . $delivery_type;
            })
            ->addColumn('action', function ($modal) {
                return '<a  href="'. route('admin.affiliate.sales.show',[$modal->id]) . '" title="View" > <i class="fa fa-eye text-primary"></i></a>';
            })
            ->setRowId('sale_{{$id}}')
            ->make(true);
    }

    public function sale_show($id) {
        $sale = Sale::findOrFail($id);
        if($sale->user_id==Auth::id()) {}
        return view('admin.pages.affiliates.show',['sale'=>$sale]);
    }
}
