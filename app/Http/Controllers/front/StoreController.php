<?php

namespace App\Http\Controllers\front;

use App\AllSlug;
use App\Category;
use App\Pin;
use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use PDF;

class StoreController extends Controller
{
    public  function index() {
        /*$pin = Pin::with('stores')->where('name','673639')->first();
        return $pin->stores;*/

        $stores = Store::where('is_active',1)->latest()->paginate(18);
        foreach($stores as $store) {
            $all_slug = AllSlug::where('causer_id',$store->id)->where('causer_type', 'App\Store')->first();
            $slug = $all_slug->slug;
            $store->slug = $slug;
        }
        return view('pages.stores',['stores' => $stores]);
    }

    public  function search(Request $request) {
        $term = $request->has('pin_search')?$request->pin_search:'';

        $pin = Pin::with('stores')->where('is_active',1)->where('name',$term)->first();
        $store = $pin ? $pin->stores()->paginate(18) : '';
        /*$store->appends(['search' => $request->pin_search]);*/
        return view('pages.stores',['stores'=>$store, 'term'=>$term]);
    }

    public  function show($user) {

        /*$categories = Category::where('parent',0)->whereNotIn('id', [11])->orderBy('id','asc')->get();
        foreach($categories as $category) {
            $category->childs = $category->child_categories();
        }*/

        /*$parent_catids = [];
        $parent_cats = [];
        $store_cats = [];
        foreach($store->categories as $store_category) {

            if(in_array($store_category->parent,$parent_catids)) {

            }else {
                $parent_category = Category::find($store_category->parent);
                $parent_cats[] = $parent_category;
                $parent_catids[] = $store_category->parent;
            }

            $store_cats[] = $store_category->id;
        }*/

        /*$store = Store::where('username',$user)->where('is_active',1)->first();
        if($store) {
            $parent_catids = [];
            $parent_cats = [];
            $store_cats = [];
            foreach($store->categories as $store_category) {
                foreach($store_category->parents as $parent) {

                    if(in_array($parent->id,$parent_catids)) {

                    }else {
                        $parent_category = Category::find($parent->id);
                        $parent_cats[] = $parent_category;
                        $parent_catids[] = $parent->id;
                    }
                }
                $store_cats[] = $store_category->id;
            }
            return view('pages.store_profile', ['store' => $store, 'categories' => $parent_cats, 'store_cats' => $store_cats]);
        }else {
            return abort(404);
        }*/
    }

    public  function download_brochure($user) {

        /*$user = 'mystore';*/

        $all_slug = AllSlug::where('slug',$user)->first();
        $causer_type = $all_slug->causer_type;
        $causer_id = $all_slug->causer_id;
        $page_slug = $all_slug->slug;

        $store = Store::where('id',$causer_id)->where('is_active',1)->first();
        if($store) {
            $parent_catids = [];
            $parent_cats = [];
            $store_cats = [];
            foreach($store->categories as $store_category) {
                foreach($store_category->parents as $parent) {

                    if(in_array($parent->id,$parent_catids)) {

                    }else {
                        $parent_category = Category::find($parent->id);
                        $parent_cats[] = $parent_category;
                        $parent_catids[] = $parent->id;
                    }
                }
                $store_cats[] = $store_category->id;
            }

            $store->username = $page_slug;

            $file_name = str_replace(' ','_',$store->name) . '_brochure.pdf';

            view()->share(['store' => $store, 'categories' => $parent_cats, 'store_cats' => $store_cats]);


            // Set extra option
            PDF::setOption(['dpi' => 100, 'defaultFont' => 'verdana']);
            // pass view file
            $pdf = PDF::loadView('pages.store_brochure')->setPaper('a4', 'portrait'); //landscape
//            PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
            // download pdf
//            return $pdf->download('pdfview.pdf');
            return $pdf->stream($file_name);




            return view('pages.store_brochure');

        }else {
            return abort(404);
        }



        /*$parent_catids = [];
        $parent_cats = [];
        $store_cats = [];
        foreach($store->categories as $store_category) {

            if(in_array($store_category->parent,$parent_catids)) {

            }else {
                $parent_category = Category::find($store_category->parent);
                $parent_category->childs = $parent_category->child_categories();
                $parent_cats[] = $parent_category;
                $parent_catids[] = $store_category->parent;
            }

            $store_cats[] = $store_category->id;
        }*/



    }

}
