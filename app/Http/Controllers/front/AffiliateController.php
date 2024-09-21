<?php

namespace App\Http\Controllers\front;

use App\Models\Affiliate;
use App\Models\AllSlug;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Clinic;
use App\Models\OneclickPurchase;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\TypeProductPivot;
use App\Models\Website\Customer;
use App\Models\Website\CustomerDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Darryldecode\Cart\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Utilities\Utility;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\InstaMojoController as InstaMojoController;
/*use App\Http\Controllers\SmsApiController as SmsApiController;*/
use App\Http\Controllers\SmsFastMsgController as SmsFastMsgController;
use App\Http\Controllers\AWLMEAPIController as AWLMEAPI;
use App\Http\Controllers\ReqMsgDTOController as ReqMsgDTO;
use App\Http\Controllers\ResMsgDTOController as ResMsgDTO;
use Laravel\Socialite\Facades\Socialite;
/*use setasign\Fpdi\Fpdi;*/
class AffiliateController extends Controller
{

    protected $redirectTo = '/';
    public function getAffiliate($id, $request) {

        if(!empty(session('kerala_h_m_affiliate')) && session('kerala_h_m_affiliate')!=$id) {
            $request->session()->forget('kerala_h_m_affiliate');
        }

        if(empty(session('kerala_h_m_affiliate'))) {
            session(['kerala_h_m_affiliate' => $id]);
        }
        return session('kerala_h_m_affiliate');
    }

    public function all_slug($affiliat_slug, $item_slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$affiliat_slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $all_slug = AllSlug::where('slug',$item_slug)->first();
        $causer_type = $all_slug->causer_type;
        $causer_id = $all_slug->causer_id;

        if($causer_type=='App\Models\Product') {
            $product = Product::where('id', $causer_id)->where('is_active',1)->first();
            if ($product) {
                $id = $product->id;
                $categories = $product->categories;
                $cat_ids = [];
                foreach ($categories as $category) {
                    $cat_ids[] = $category->id;
                }
                /*$relatedProducts = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($cat_ids, $id) {
                    $query->whereIn('categories.id', $cat_ids)->whereNotIn('products.id', [$id]);
                })->latest()->get();*/


                /*DB::table('products')
                    ->join('category_product', function ($join) {
                        $join->on('products.id', '=', 'category_product.product_id')
                            ->where('contacts.user_id', '>', 5);
                    })
                    ->get();*/

                $relatedProducts = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($cat_ids, $id) {
                    $query->whereIn('categories.id', $cat_ids)->whereNotIn('products.id', [$id]);
                })->join('affiliate_product', function ($join) use($affiliate_id) {
                    $join->on('products.id', '=', 'affiliate_product.product_id')
                        ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
                })
                    ->select('products.*')
                    ->get();




                foreach ($relatedProducts as $relatedProduct) {
                    $all_slug_r = AllSlug::where('causer_id', $relatedProduct->id)->where('causer_type', 'App\Models\Product')->first();
                    $relatedslug = $all_slug_r->slug;
                    $relatedProduct->slug = $relatedslug;
                }

                /*return $relatedProducts;*/

                $affiliate_product = DB::table('affiliate_product')->where(['affiliate_id' => $affiliate_id, 'product_id' => $causer_id])->first();

                return view('affiliates.product_detail', ['product' => $product, 'relatedProducts' => $relatedProducts, 'slug' => $item_slug,'affiliate_product'=>$affiliate_product]);
            }else {
                abort('404');
            }
        }
        elseif($causer_type=='App\Models\Brand') {

            $brand = Brand::findOrFail($causer_id);

            if($brand) {
                $products = Product::whereHas('brands', function($query) use($causer_id) {
                    $query->whereIn('brands.id', [$causer_id]);
                })->join('affiliate_product', function ($join) use($affiliate_id) {
                    $join->on('products.id', '=', 'affiliate_product.product_id')
                        ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
                })->orderBy('products.created_at','desc')->paginate(24);
                foreach($products as $product) {
                    $all_slug = AllSlug::where('causer_id',$product->product_id)->where('causer_type', 'App\Models\Product')->first();
                    $product->slug = $all_slug->slug;
                    $product->id = $product->product_id;
                }
                return view('affiliates.brand_products',['brand' => $brand, 'products'=>$products, 'slug' => $item_slug]);
            }else {
                return abort(404);
            }

        }elseif($causer_type=='App\Models\Category') {

            $mainCategory = Category::findOrFail($causer_id);

            $products = Product::where('is_active', 1)
                ->whereHas('categories', function ($query) use ($mainCategory) {
                    $query->where('categories.id', $mainCategory->id);
                })
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
                ->join('affiliate_product', function ($join) use ($affiliate_id) {
                    $join->on('products.id', '=', 'affiliate_product.product_id')
                    ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
                })
                    ->select('products.*')
                    ->paginate(24);

                    foreach ($products as $product) {
                        $all_slug = AllSlug::where('causer_id', $product->id)->where('causer_type', 'App\Models\Product')->first();
                        $slug_al = $all_slug->slug;
                        $product->slug = $slug_al;
                    }

            return view('affiliates.products', ['category' => $mainCategory, 'products' => $products]);

            // $child_count = $mainCategory->childs->count();
            // if($child_count==0) {
            //     $products = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($causer_id) {
            //         $query->whereIn('categories.id', [$causer_id]);
            //     })->latest()->paginate(24);

            //     foreach ($products as $product) {
            //         $all_slug = AllSlug::where('causer_id', $product->id)->where('causer_type', 'App\Models\Product')->first();
            //         $product->slug = $all_slug->slug;
            //     }
            //     return view('affiliates.products', ['category' => $mainCategory, 'products' => $products]);
            // }else {
            //     $childs = Category::where('is_active',1)->
            //     join('category_parent', function ($join) use($causer_id) {
            //         $join->on('categories.id', '=', 'category_parent.category_id')
            //             ->where('category_parent.parent_id', '=', $causer_id);
            //     })->
            //     join('affiliate_category', function ($join) use($affiliate_id) {
            //         $join->on('categories.id', '=', 'affiliate_category.category_id')
            //             ->where('affiliate_category.affiliate_id', '=', $affiliate_id);
            //     })->get();

            //     $child_product_array=[];
            //     $child_product_id_array=[];
            //     foreach($childs as $child_category) {
            //         $child_products = Category::findOrFail($child_category->id)->products;
            //         if(!empty(json_decode($child_products))) {
            //             foreach($child_products as $child_product) {
            //                 $hasProduct = DB::table('affiliate_product')->where('affiliate_id',$affiliate_id)->where('product_id',$child_product->id)->first();

            //                 if($hasProduct) {
            //                     if (!in_array($child_product->id, $child_product_id_array)) {
            //                         array_push($child_product_array, $child_product);
            //                         $child_product_id_array[] = $child_product->id;
            //                         $child_product->price = $child_product->min_price();
            //                         $child_product->min_mrp = $child_product->min_mrp();

            //                         $all_slug = AllSlug::where('causer_id', $child_product->id)->where('causer_type', 'App\Models\Product')->first();
            //                         $slug = $all_slug->slug;
            //                         $child_product->slug = $slug;
            //                     }
            //                 }


            //             }

            //         }
            //     }

            //     return view('affiliates.subcategories',['category' => $mainCategory,'products'=>$child_product_array]);
            // }
        }
    }

    public  function about_us ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.about');
    }

    public  function contact ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.contact');
    }

    public function contact_send($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        $affiliate = Affiliate::find(session('kerala_h_m_affiliate'));


        $subject = 'Enquiry via contact form of Kerala Health Mart';
        $to = !empty($affiliate->contact_email) ? $affiliate->contact_email : "keralahealthmart@gmail.com"; //
        $from = "noreply@keralahealthmart.com";
        // $send = Mail::send('mails.contact_us', ['data' => $request->all()], function ($message) use ($from, $to, $subject) {
        //     $message->from($from, config('app.name','Kerala Health Mart'));
        //     $message->to($to);
        //     $message->subject($subject);
        //     return 1;

        // });
    }

    public  function payments ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.payments');
    }

    public  function disclaimer ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.disclaimer');
    }

    public  function shipping ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.shipping');
    }

    public  function cancellation ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.cancellation');
    }

    public  function privacy_policy ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.privacy_policy');
    }

    public  function terms_conditions ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.terms_conditions');
    }

    public  function affiliate ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return view('affiliates.affiliate');
    }

    public  function category_all ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $affiliate = Affiliate::findOrFail($affiliate_id);
        $categories = $affiliate->main_categories->where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER]);
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

        // $mainCategories = $affiliate->main_categories->where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER]);

        // foreach($mainCategories as $mainCategory) {
        //     $child_product_array=[];
        //     $child_product_id_array=[];
        //     $child_cat_ids=[];
        //     foreach($mainCategory->childs as $child_category) {
        //         $child_cat_ids[] = $child_category->id;
        //     }
        //     $child_products = Product::where('is_active', 1)->whereHas('categories', function ($query) use ($child_cat_ids) {
        //         $query->whereIn('categories.id', $child_cat_ids);
        //     })->join('affiliate_product', function ($join) use($affiliate_id) {
        //         $join->on('products.id', '=', 'affiliate_product.product_id')
        //             ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
        //     })
        //         ->select('*')
        //         ->get();

        //     foreach ($child_products as $child_product) {
        //         if (!in_array($child_product->product_id, $child_product_id_array)) {
        //             array_push($child_product_array, $child_product);
        //             $child_product_id_array[] = $child_product->product_id;
        //             $all_slug = AllSlug::where('causer_id',$child_product->product_id)->where('causer_type', 'App\Models\Product')->first();
        //             $slug_al = $all_slug->slug;
        //             $child_product->slug = $slug_al;
        //             $child_product->id = $child_product->product_id;
        //             $child_product->price = $child_product->min_price();
        //             $child_product->min_mrp = $child_product->min_mrp();
        //         }
        //     }

        //     $mainCategory->products = $child_product_array;
        // }

        return view('affiliates.categories',['categories' => $categories]); //,'mainCategories'=>$mainCategories
    }

    public  function search_on_type ($slug, Request $request) {
        $cat_id = $request->cat_id;
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;

        $term = $request->has('term')?$request->term:'';
        $term_display = $request->has('term')?$request->term:'';
        /*$allproducts = Product::where('name', 'like', '%'.$term.'%')->where('is_active',1)->get();*/


        $products = Product::
        join('affiliate_product', function ($join) use($affiliate_id) {
            $join->on('affiliate_product.product_id', '=','products.id')
                ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
        });

        if ($request->has('cat_id')&&!empty($cat_id)) {
            $products->join('category_product', function ($join) use ($request) {
                $join->on('category_product.product_id','=','products.id')
                ->where('category_product.category_id', '=', $request->cat_id);

            });
        }

        if(!empty($term)) {
           $products->where('name', 'like', '%'.$term.'%');
        }

        if ($request->has('cat_id')&&!empty($cat_id)) {
            // $products->join('category_product', function ($join) use ($request) {
            //     $join->on('category_product.product_id','=','products.id')
            //     ->where('category_product.category_id', '=', $request->cat_id);

            // });

            // ->join('affiliate_category', function ($join) {
            //     $join->on('affiliate_category.category_id', '=', 'categories.id')
            //         ->where('affiliate_category.affiliate_id', session('kerala_h_m_affiliate'));
            // });

            // $products->whereHas('categories', function ($query) use ($request) {
            //     $query->where('categories.id', $request->cat_id);
            // });
        }

        $products = $products->get();



        $htmlData= '';

        $produCount = 1;
        foreach($products as $allproduct) {
            if($produCount <=10) {
                if ($allproduct->image != '') {
                    $image_thumb = asset(Utility::DEFAULT_STORAGE . Product::FILE_DIRECTORY .  '/' . $allproduct->image);
                } else {
                    $image_thumb = asset('images/no-image.jpg');
                }

                $all_slug = AllSlug::where('causer_id',$allproduct->product_id)->where('causer_type', 'App\Models\Product')->first();
                $product_slug = $all_slug->slug;
                $allproduct->slug = $product_slug;
                $allproduct->id = $allproduct->product_id;

                $htmlData .= '<div class="display_box" align="left">
                    <a href="' . route('affiliate.all.slug', [$slug, $product_slug]) . '">
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

    public  function search_results ($slug, Request $request) {
        $cat_id = $request->cat_id;
        $subcat_id = $request->subcat_id;

        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $term = $request->has('term')?$request->term:'';
        $term_display = $request->has('term')?$request->term:'';

        $products = Product::
            join('affiliate_product', function ($join) use($affiliate_id) {
                $join->on('products.id', '=', 'affiliate_product.product_id')
                    ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
            });
        if(!empty($term)) {
            $products = $products->where('name', 'like', '%'.$term.'%');
        }
        if ($request->has('cat_id')) {
            $products->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->cat_id);
            });
        }

        $products = $products->get();

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->product_id)->where('causer_type', 'App\Models\Product')->first();
            $slug = $all_slug->slug;
            $product->slug = $slug;
            $product->id = $product->product_id;
        }

        return view('affiliates.search_products',['products' => $products, 'term'=>$term_display, 'selected_cat' => $cat_id, 'selected_subcat' => $subcat_id]);
    }

    public  function featured_products ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        $products = Product::where('is_featured',1)->where('is_active',1)->latest()->get();
        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $slug = $all_slug->slug;
            $product->slug = $slug;
        }
        return view('affiliates.featured',['products' => $products]);
    }

    public  function sub_category_all ($slug, $id,Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        $mainCategory = Category::findOrFail($id);
        /*$category->childs = $category->child_categories();*/

        $childs = Category::where('is_active',1)->
        join('category_parent', function ($join) use($id) {
            $join->on('categories.id', '=', 'category_parent.category_id')
                ->where('category_parent.parent_id', '=', $id);
        })->
        join('affiliate_category', function ($join) use($affiliate_id) {
            $join->on('categories.id', '=', 'affiliate_category.category_id')
                ->where('affiliate_category.affiliate_id', '=', $affiliate_id);
        })->get();


        $child_product_array=[];
        $child_product_id_array=[];
        foreach($childs as $child_category) {
            $child_products = Category::findOrFail($child_category->id)->products;
            /*$child_products = Product::where('is_active',1)->
            join('category_product', function ($join) use($child_category) {
                $join->on('products.id', '=', 'category_product.product_id')
                    ->where('category_product.category_id', '=', $child_category->id);
            })->
            join('affiliate_product', function ($join) use($id) {
                $join->on('products.id', '=', 'affiliate_product.product_id');
            })->get();*/


            if(!empty(json_decode($child_products))) {
                foreach($child_products as $child_product) {
                    $hasProduct = DB::table('affiliate_product')->where('affiliate_id',$affiliate_id)->where('product_id',$child_product->id)->first();

                    if($hasProduct) {
                        if (!in_array($child_product->id, $child_product_id_array)) {
                            array_push($child_product_array, $child_product);
                            $child_product_id_array[] = $child_product->id;
                            $child_product->price = $child_product->min_price();
                            $child_product->min_mrp = $child_product->min_mrp();

                            $all_slug = AllSlug::where('causer_id', $child_product->id)->where('causer_type', 'App\Models\Product')->first();
                            $slug = $all_slug->slug;
                            $child_product->slug = $slug;
                        }
                    }


                }

            }
        }

        return view('affiliates.subcategories',['category' => $mainCategory,'products'=>$child_product_array]);
    }

    public  function category_products ($slug, $id,Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $category = Category::findOrFail($id);

        $products = Product::where('is_active',1)->whereHas('categories', function($query) use($id) {
            $query->where('categories.id', $id);
        })
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
        ->join('affiliate_product', function ($join) use ($affiliate_id) {
            $join->on('products.id', '=', 'affiliate_product.product_id')
            ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
        })
        ->latest()->paginate(24);

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $product->slug = $all_slug->slug;
        }
        return view('affiliates.products',['category' => $category, 'products'=>$products]);
    }

    public  function offer_products ($slug, Request $request) {
        $id = Utility::CATEGORY_ID_OFFER;
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $affiliate = Affiliate::find($affiliate_id);

        $products = $affiliate->offer_products;

        $category = Category::findOrFail($id);

        /*$products = Product::where('is_active',1)->whereHas('categories', function($query) use($id) {
            $query->whereIn('categories.id', [$id]);
        })->latest()->paginate(24);*/

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->id)->where('causer_type', 'App\Models\Product')->first();
            $product->slug = $all_slug->slug;
        }
        return view('affiliates.products',['category' => $category, 'products'=>$products, 'pagination'=>0]);
    }

    public  function brands ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $affiliate = Affiliate::find($affiliate_id);
        /*$brands = Brand::all();*/
        $brands = $affiliate->brands;
        foreach($brands as $brand) {
            $all_slug = AllSlug::where('causer_id',$brand->id)->where('causer_type', 'App\Models\Brand')->first();
            $brand->slug = $all_slug->slug;
        }
        return view('affiliates.brands',['brands' => $brands]);
    }

    public function oneclick_purchase ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;

        $affiliate = Affiliate::find($affiliate_id);
        $affiliate_user = $affiliate->user_id;


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
            $input['user_id'] = $affiliate_user;
            $purchase->fill($input)->save();
        }
        return $purchase;
    }

    public function prescription ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;

        $affiliate = Affiliate::find($affiliate_id);
        $affiliate_user = $affiliate->user_id;

        $rules = [
            'phone_prescription' => 'required|max:255',
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
            $prescription = new Prescription;
            $input = $request->all();
            $input['user_id'] = $affiliate_user;
            $prescription->fill($input)->save();

            if($request->hasFile('image_prescription')) {
                $image_prescription = $request->file('image_prescription');
                $image_prescription_name = $prescription->id . '_' . str_replace(' ','_',$image_prescription->getClientOriginalName());
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Prescription::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image_prescription']['tmp_name'];
                $contents = file_get_contents($realPath);
                $image_prescription_path = Prescription::FILE_DIRECTORY . '/'. $image_prescription_name;
                Storage::disk('prescriptions')->put($image_prescription_name, $contents);
                $prescription->image_prescription = $image_prescription_path;
                $prescription->save();
            }
        }
        return $prescription;
    }

    public function get_price ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;

        $product_id = $request->product_id;
        $type_size = $request->type_size;
        $quantity = (int)$request->quantity;

        $product = Product::find($product_id);

        $max_price = $product->productTypePrice($type_size)['max'];

        $type = TypeProductPivot::where('type_id',$type_size)->where('product_id',$product_id)->first();
        $type_id = $type->id;

        $affiliate_product_type = DB::table('affiliate_product_type')->where(['affiliate_id' => $affiliate_id, 'product_id' => $product_id, 'type_id' => $type_size])->first();

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

        /*$price_khm = PriceDetail::where('tp_pivot_id',$type_id);
        $price_khm = $price_khm->where(function ($query) use ($quantity) {
            $query->where('quantity_from', '<=', $quantity);
        });
        $price_khm = $price_khm->orderBy('quantity_from','desc')->first(['price']);*/

        /*$prices = PriceDetail::where('tp_pivot_id',$type_id)->get();*/

        $prices = $type->prices;

        /*$price = $prices->min()->price;*/
        /*if(empty($affiliate_product_type->profit)) {
            $price = Utility::getAffiliatePrice($product_id,$type_size)['khm'];
        }else {
            $price = Utility::getAffiliatePrice($product_id,$type_size)['cost'] + (Utility::getAffiliatePrice($product_id,$type_size)['cost'] * ($affiliate_product_type->profit/100));
        }*/


        if(!empty($affiliate_product_type->profit)) {
            $profit_type = $affiliate_product_type->profit_type;
            $basic_cost = Utility::getAffiliatePrice($product_id,$type_size)['cost'];
            if($profit_type == Utility::PROFIT_TYPE_PERCENTAGE) {
                $price =  round($basic_cost + ($basic_cost * ($affiliate_product_type->profit/100)),2);
            }else if($profit_type == Utility::PROFIT_TYPE_MARGIN) {
                $price =  round($basic_cost + $affiliate_product_type->profit,2);
            }else {
                $price =  round($affiliate_product_type->profit,2);
            }

        }else {
            $price =  round(Utility::getAffiliatePrice($product_id,$type_size)['khm'],2);
        }


        if(empty($type->mrp) || ($type->mrp == 0)) {
            $type->mrp = $max_price;
        }

        if(!empty($max_price) && ($max_price != $price) && ($max_price!=0)) {
            $discount = (($type->mrp-$price)/$type->mrp)*100;
            $type->discount = round($discount,0);
        }else {
            $type->discount = 0;
            $type->mrp = 0;
        }

        return ['price' => $price, 'quantity' => $quantity, 'type' => $type];

    }

    public function cart_show($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        if(\Cart::isEmpty()) {
            $cartCollection = '';
        }
        else {
            $cartCollection = \Cart::getContent();
            $cartCollection = json_decode($cartCollection);
        }
        return view('affiliates.cart',['cartCollection'=>$cartCollection, 'grandTotal'=>\Cart::getTotal()]);
    }

    public function cart_add($slug, Request $request) {
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

        $actual_price = $this->actual_price ($request->id,$request->type, $slug);
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

    public function cart_update($slug, $item, Request $request) {

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

        $actual_price = $this->actual_price ($cartItem->id,$cartItem->attributes->type, $slug);

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

    public function cart_delete($slug, $item) {
        $remove = \Cart::remove($item);
        if(\Cart::isEmpty()) return 1;
        else return 0;
    }

    public function cart_clear(Request $request) {
        $clear = \Cart::clear();
        $request->session()->forget('kerala_h_m_ship_option');
        if($clear) return 1;
        else return 0;
    }

    public function actual_price ($product_id,$type_size, $slug) {

        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;

        $type = TypeProductPivot::where('type_id',$type_size)->where('product_id',$product_id)->first();
        $type_id = $type->id;

        $affiliate_product_type = DB::table('affiliate_product_type')->where(['affiliate_id' => $affiliate_id, 'product_id' => $product_id, 'type_id' => $type_size])->first();

        if(empty($affiliate_product_type->profit)) {
            $price = Utility::getAffiliatePrice($product_id,$type_size)['khm'];
        }else {

            $profit_type = $affiliate_product_type->profit_type;
            $basic_cost = Utility::getAffiliatePrice($product_id,$type_size)['cost'];
            if($profit_type == Utility::PROFIT_TYPE_PERCENTAGE) {
                $price =  round($basic_cost + ($basic_cost * ($affiliate_product_type->profit/100)),2);
            }else if($profit_type == Utility::PROFIT_TYPE_MARGIN) {
                $price =  round($basic_cost + $affiliate_product_type->profit,2);
            }else {
                $price =  round($affiliate_product_type->profit,2);
            }
            /*$price = Utility::getAffiliatePrice($product_id,$type_size)['cost'] + (Utility::getAffiliatePrice($product_id,$type_size)['cost'] * $affiliate_product_type->profit);*/
        }

        return $price;
    }

    public function checkout_login($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        return view('affiliates.checkout_login',['grandTotal'=>\Cart::getTotal()]);
    }

    public function checkout_address($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        if(\Cart::isEmpty()) return redirect()->route('all.slug',$slug);
        $customer_id = Auth::guard('customer')->user()->id;
        $customerDetails = CustomerDetail::where('customer_id',$customer_id)->first();
        $states = DB::table('states')->select('id','name')->get();
        return view('affiliates.checkout_address',['states'=> $states, 'customerDetails' => $customerDetails, 'grandTotal'=>\Cart::getTotal()]);
    }

    public function checkout_address_store($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $validator = $this->address_validator($request->all());
        $customer_id = Auth::guard('customer')->user()->id;
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('affiliate.checkout.address',$slug)
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

    public function subcategory_list($slug, Request $request) {
        $affiliate = Affiliate::find(session('kerala_h_m_affiliate'));
        foreach($affiliate->categories as $index => $category) {
            /*$category->parents = $category->parents;*/
            if(!(empty(json_decode($category->parents)))) {
                foreach ($category->parents as $parent) {
                    if ($parent->id == $request->category_id) {
                    }else {
                        unset($affiliate->categories[$index]);
                    }
                }
            }else {
                unset($affiliate->categories[$index]);
            }

        }

        $data[]= '';
        foreach($affiliate->categories as $child) {
            $selected = ($request->has('subcategory_id')) && ($request->subcategory_id == $child->id) ? 'selected' : '';
            $data[] = '<option value="' . $child->id . '"' . $selected . ' >'. $child->name . '</option>';
        }
        return $data;
    }

    public function payment_options($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        if(\Cart::isEmpty()) return redirect()->route('all.slug',$slug);
        return view('affiliates.payment_options',['grandTotal'=>\Cart::getTotal()]);
    }

    public function payment_options_store($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $affiliate = Affiliate::find($affiliate_id);
        $affiliate_user = $affiliate->user_id;

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
            $success_route = route('affiliate.product.cart',$slug);
            return response()->json($success_route);
        }
        $cartTotal = \Cart::getTotal();

        $delivery = Utility::getDeliveryCharge();
        $deliveryCharge = $delivery['cost'];

        foreach($cartCollection as $index => $cartitem) {
            $stock = Utility::get_stock($cartitem->id,$cartitem->attributes->type);
            if($stock < $cartitem->quantity) {
                $success_route = route('affiliate.product.cart',$slug);
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
        $sale->user_id = $affiliate_user;
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
        $redirectUrl = route('affiliate.checkout.payment.success',$slug);

        //SEND SMS START
        $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
        $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
        $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
        $smsText = config('app.name') . ': Your order ' . $sale->order_no . ' amounting to Rs. ' . $amount . ' placed. We will send you an update when the order is shipped.';
        $mblno = Setting::IND_CODE . $sale->customer->phone;
        //SEND SMS END

        if($request->payment_option == Utility::PAYMENT_ONLINE) {
            /*$sendPayment = new InstaMojoController($amount,$purpose,$phone,$email,$name,$redirectUrl);
            $createRequest = $sendPayment->createRequest();
            $success_route = route('affiliate.payment.online',[$slug, 'request_url' => $createRequest]);
            return response()->json($success_route);*/
            return $this->meTrnPay($order_id,$amount_paisa,$customer_details->name,$mblno);
        }else {
            $clear = \Cart::clear();
            $request->session()->forget('kerala_h_m_ship_option');
            $success_route = route('affiliate.checkout.payment.success',$slug);

            if(!empty($sale->customer->phone)) {
                //$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');
                $sendSMS = $this->sendsms($sale->customer->phone, $smsText);
            }

            //return response()->json($success_route);
            // return redirect()->route($success_route);
            $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
            return view('affiliates.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails, 'is_paid' => 0]);

        }

    }

    /*public function payment_success($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $customer_id = Auth::guard('customer')->user()->id;
        if($customer_id) {
            $last_sale = Sale::where('customer_id',$customer_id)->latest()->first();
            $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
            return view('affiliates.pay_success',['sale' => $last_sale, 'customerDetails' => $customerDetails]);
        }else {
            return redirect()->route('index');
        }

    }*/

    public function payment_success($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

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
                //return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails, 'is_paid' => 1]);
                return view('affiliates.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails, 'is_paid' => 1]);
            }else {
                return redirect()->route('index');
            }
        }else {
            $order_no = $response->getOrderId();
            $sale = Sale::where('order_no',$order_no)->latest()->first();
            $customer_id = Auth::guard('customer')->user()->id;
            if($customer_id) {
                $customerDetails = CustomerDetail::with('customer')->where('customer_id',$customer_id)->first();
                //return view('pages.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails, 'is_paid' => 0]);
                return view('affiliates.pay_success',['sale' => $sale, 'customerDetails' => $customerDetails, 'is_paid' => 0]);
            }else {
                return redirect()->route('index');
            }
        }

    }

    public  function online_pay ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        return view('affiliates.online_pay');
    }







    public  function profile ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $customer_id = Auth::guard('customer')->user()->id;
        $customer = Customer::find($customer_id);
        $states = DB::table('states')->select('id','name')->get();
        return view('affiliates.profile',['customer'=>$customer, 'states' =>$states]);
    }

    public function profile_update ($slug, Request $request) {
        /*return $request->name;*/
        $validator = $this->profile_validator($request->all());
        $customer_id = Auth::guard('customer')->user()->id;

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('affiliate.profile',$slug)
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

    public function cancel_order($slug , Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

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

    public  function my_orders ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $customer_id = Auth::guard('customer')->user()->id;
        $sales = Sale::where('customer_id',$customer_id)->where('status','!=',Utility::SALE_STATUS_CANCELLED)->latest()->paginate(10);
        return view('affiliates.myorders',['sales'=>$sales]);
    }

    public function utr_update($slug, Request $request) {
        $sale = Sale::find($request->sale_id);
        if($sale->is_paid) {

        }else {
            $input['utr_no'] = $request->utr_no;
            $input['is_utr_cust'] = 1;
            $sale->fill($input)->save();
            return response()->json(['utr' => $request->utr_no]);
        }
    }

    public  function settings_account ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        return view('affiliates.account_settings');
    }

    public  function delete_account ($slug, Request $request) {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        return view('affiliates.delete_account');
    }

    public  function delete_account_act ($slug, Request $request) {
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
            $success_route = route('all.slug',$slug);
            return response()->json($success_route);
        }else {
            $success_route = route('affiliate.delete.account',[$slug,'error_msg',1]);
            return response()->json($success_route);
        }

    }

    /*public function refreshCaptcha($slug)
    {
        return response()->json(['captcha'=> captcha_img('khm')]);
    }*/

    // public function pay_later($slug, $sale_id, Request $request) {

    //     $sale = Sale::find($sale_id);
    //     if(!$sale->is_paid) {
    //         $customerDetails = CustomerDetail::where('customer_id',$sale->customer->id)->first();

    //         $amount= $sale->sub_total+$sale->delivery_charge;
    //         $purpose = $sale->order_no;
    //         $phone = Auth::guard('customer')->user()->phone;
    //         $email = Auth::guard('customer')->user()->email;
    //         $name = $customerDetails->name;
    //         $redirectUrl = route('affiliate.checkout.payment.success',$slug);

    //         $sendPayment = new InstaMojoController($amount,$purpose,$phone,$email,$name,$redirectUrl);
    //         $createRequest = $sendPayment->createRequest();
    //         $success_route = route('affiliate.payment.online',[$slug, 'request_url' => $createRequest]);
    //         return response()->json($success_route);
    //     }else {
    //         $myorders = route('myorders');
    //         return response()->json($myorders);
    //     }

    // }








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

    // public  function clinics($slug) {
    //     $clinics = Clinic::where('is_active',1)->latest()->paginate(18);
    //     foreach($clinics as $clinic) {
    //         $all_slug = AllSlug::where('causer_id',$clinic->id)->where('causer_type', 'App\Models\Clinic')->first();
    //         $slug = $all_slug->slug;
    //         $clinic->slug = $slug;
    //     }
    //     return view('affiliates.services',['clinics' => $clinics]);
    // }

    // public  function clinic_search ($slug, Request $request) {
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

    //     return view('affiliates.services',['clinics' => $allclinics, 'term'=>$term_display, 'selected_type' => $type_id, 'selected_district' => $district_id]);
    // }

    public function test($slug) {
        $term = "a";
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;

        /*$allproducts = Product::where('is_active',1)->where('name', 'like', '%'.$term.'%');
        if ($request->has('subcat_id')) {
            $allproducts->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->subcat_id);
            });
        }

        $allproducts = $allproducts->get();*/





        /*$affiliate = Affiliate::findOrFail($affiliate_id);
        $products = $affiliate->products->where('name', 'like', '%'.$term.'%');*/


        $products = Product::where('name', 'like', '%'.$term.'%')
            ->join('affiliate_product', function ($join) use($affiliate_id) {
                $join->on('products.id', '=', 'affiliate_product.product_id')
                    ->where('affiliate_product.affiliate_id', '=', $affiliate_id);
            })
            ->get();

        foreach($products as $product) {
            $all_slug = AllSlug::where('causer_id',$product->product_id)->where('causer_type', 'App\Models\Product')->first();
            $slug = $all_slug->slug;
            $product->slug = $slug;
        }
        return $products;
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

    public function redirectToGmail($slug, Request $request)
    {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        return Socialite::driver('google')->redirect();
    }
    public function handleGmailCallback($slug, Request $request)
    {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);
        // $user = Socialite::driver('google')->user();
        $customer = Socialite::driver('google')->user();

        $authCustomer = $this->findOrCreateUser($slug, $customer, 'google', $request);
        // Auth::login($authUser, true);
        $this->guard()->login($authCustomer);

        if(\Cart::isEmpty()) {
            return redirect('all.slug',$slug);
        }else {
            return redirect()->route('affiliate.product.cart',$slug);
        }
        // return response()->json($customer);
    }

    public function findOrCreateUser($slug, $googleUser, $provider, $request)
    {
        $affiliate_slug = AllSlug::where('slug',$slug)->first();
        $affiliate_id = $affiliate_slug->causer_id;
        $affiliate_sess = $this->getAffiliate($affiliate_id, $request);

        $authUser = Customer::where('provider_id', $googleUser->id)->first();

        if ($authUser) {
            return $authUser;
        }

        $customer = new Customer;

        $customer->fill([
            'email' => $googleUser->email,
            'provider' => $provider,
            'provider_id' => $googleUser->id,
            'status' => 1,
            'is_active' => 1,
            'is_access' => 1,
            // 'phone' => $data['phone'],
            // 'password' => bcrypt($data['password'])
        ]);
        $customer->save();

        $customer_detail = new CustomerDetail;
        $customer_detail->fill([
            'customer_id' => $customer->id,
            'name' => $googleUser->name,
            'address' => [],
            'profile_pic' => $googleUser->avatar,
        ]);
        $customer_detail->save();

        return $customer;
    }

}
