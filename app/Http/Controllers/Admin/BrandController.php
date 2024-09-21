<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\AllSlug;
use App\Models\Category;
use App\Models\PriceDetail;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Services\Slug;
use App\Models\TypeProductPivot;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ResizeImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Utilities\Utility;
use Darryldecode\Cart\Validators\Validator;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public  function index() {
        return view('admin.pages.brands.index');
    }
    public  function data() {
        $brands = User::whereHas('roles' , function($q){
            $q->where('id', Utility::BRAND_ROLE_ID);
        })->select()->latest();
        return Datatables::of($brands)
            ->rawColumns(['name','email','image','action'])
            ->editColumn('name', function ($modal) {
                $all_slug = AllSlug::where('causer_id',$modal->brand->id)->where('causer_type', 'App\Models\Brand')->first();
                $slug = !empty($all_slug) ? $all_slug->slug : '';
                $data = '<p>' . $modal->name . '</p>';
                $data .= '<a href="'. config('app.website_url') . '/' . $slug . '" target="_blank">'. config('app.website_url') . '/' . $slug . '</a>';
                return $data;
            })

            ->addColumn('image', function ($modal) {
                $main_image = !empty($modal->brand->image) ? '<img src="' . asset(Utility::DEFAULT_STORAGE . Brand::FILE_DIRECTORY . '/'. $modal->brand->image) . '" alt="" height="50" />' : '';
                return $main_image;
            })
            ->editColumn('email', function ($modal) {
                return '<p>' . $modal->email . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-brand" data-target="' . route('admin.brands.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.brands.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.brands.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('brand_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $brandLists = Brand::all();
        $returnHTML = view('admin.pages.brands.add-modal-form',['brandLists' => $brandLists])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public  function store(Request $request) {

        $validated = request()->validate([
            'name' => 'required|unique:brands,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);


        $user = new User;
        $user_input = $request->only(['email','name']);
        $user_input['email'] = $request->email;
        $user_input['username'] = $request->email;
        $user_input['password'] = bcrypt($request->password);
        $user_input['is_active'] = 1;
        $user->fill($user_input)->save();

        $user_details = new UserDetail;
        $user_details_input = [];
        $user_details_input['user_id'] = $user->id;
        $user_details->fill($user_details_input)->save();

        $brand = new Brand;
        $input = $request->only(['name','site_title','site_keywords','site_description']);
        $input['user_id'] = $user->id;
        $brand->fill($input)->save();

        $slug = new Slug();
        $slug_data = $slug->createSlug($request->name);

        $all_slug = new AllSlug();
        $all_slug->fill([
            'causer_id' => $brand->id,
            'causer_type' => 'App\Models\Brand',
            'slug' => $slug_data,
        ]);
        $all_slug->save();

        DB::table('role_user')->insert(
            ['user_id' => $user->id, 'role_id' => Utility::BRAND_ROLE_ID]
        );

        $imgUploadSize = Utility::IMAGE_BRAND;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $file_name = Utility::addUnderScore($brand->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Brand::FILE_DIRECTORY);

            // $path = public_path('images/');
            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

                $image = ResizeImage::make($file->path());
                $image->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name,90);

            $brand->image = $file_name;
            $brand->save();
        }

        $imgUploadSizeOriginal = Utility::IMAGE_INDIVIDUAL_BRAND;
        $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
        $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

        if($request->hasFile('images')) {
            $images = $request->file('images');

            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Brand::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

            $images_path = [];
            foreach($images as $index => $image) {
                $image_name_o_original = Utility::addUnderScore($brand->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());

                $image_m = ResizeImage::make($image->path());
                $image_m->resize($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image_m->save($destinationPath.'/'.$image_name_o_original,90);

                $images_path[] = $image_name_o_original;
            }
            $brand->images = $images_path;
        }





        if ($request->ajax()) {
            return response()->json(['success' => 'New Brand added successfully']);
        } else {
            return redirect()->route('admin.brands.index')->with(['success' => 'New Brand added successfully']);
        }
    }

    public function edit($id)
    {
        /*$brand = Brand::findOrFail($id);

        return response()->json(['brand' => $brand]);*/
    }

    public function edit_modal($id)
    {
        $user = User::findOrFail($id);

        /*$brand = Brand::where('user_id',$id)->first();*/
        $brandLists = Brand::where('id','!=',$id)->get();

        $all_slug = AllSlug::where('causer_id',$user->brand->id)->where('causer_type', 'App\Models\Brand')->first();
        if($all_slug) {
            $slug = $all_slug->slug;
        }else {
            $slug = null;
        }

        $returnHTML = view('admin.pages.brands.add-modal-form',['brandLists' => $brandLists, 'user' => $user, 'slug'=>$slug])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public function update(Request $request, $id)
    {
        // $rules = [
        //     'name' => 'required'
        // ];
        // $messages = [
        //     'required' => 'The :attribute field is required.',
        // ];
        // $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     if($request->ajax()) {
        //         return response()->json($validator->errors(), 422);
        //     } else {
        //         return redirect()->route('admin.brands.edit', ['id' => $id])
        //             ->withErrors($validator)
        //             ->withInput($request->all());
        //     }
        // }

        $validated = request()->validate([
            'name' => 'required|max:255|unique:users,name,'.$id,
        ]);

        $user = User::findOrFail($id);

        $brand = Brand::where('user_id', $user->id)->first();
        $input = $request->only(['name','site_title','site_keywords','site_description']);

        $slug = new Slug();
        $all_slug = AllSlug::where('causer_id',$brand->id)->where('causer_type', 'App\Models\Brand')->first();


        if($all_slug) {
            if ($brand->name != $request->name) {
                $slug_data = $slug->createSlug($request->name, $id);
                $all_slug->fill(['slug' => $slug_data])->save();
            }
        }else {
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $brand->id,
                'causer_type' => 'App\Models\Brand',
                'slug' => $slug_data,
            ]);
            $all_slug->save();
        }

        $user_input = $request->only(['email','name']);
        $user_input['username'] = $request->email;
        if(!empty($request->password)) {
            $user_input['password'] = bcrypt($request->password);
        }
        $user->fill($user_input)->save();

        $imgUploadSize = Utility::IMAGE_BRAND;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        if(isset($request->is_image) && ($request->is_image == 0)){
            $this->destroy_image($brand->image);
            $input['image'] = null;
        }

        if($request->hasFile('image')) {

            $file = $request->file('image');
            $file_name = Utility::addUnderScore($brand->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Brand::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

                $image = ResizeImage::make($file->path());
                $image->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name,90);

            // $brand->image = $file_name;
            $input['image'] = $file_name;
        }

        $imgUploadSizeOriginal = Utility::IMAGE_INDIVIDUAL_BRAND;
        $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
        $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

        if(isset($request->is_images) && ($request->is_images == 0)){
            if(!empty($brand->images)) {
                foreach ($brand->images as $brand_image) {
                    $this->destroy_image($brand_image);
                }
            }
            $input['images'] = null;
        }

        if($request->hasFile('images')) {
            // if(!empty($brand->images)) {
            //     foreach ($brand->images as $brand_image) {
            //         $this->destroy_image($brand_image);
            //     }
            // }
            $images = $request->file('images');

            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Brand::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

            $images_path = [];
            foreach($images as $index => $image) {
                $image_name_o_original = Utility::addUnderScore($brand->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());

                $image = ResizeImage::make($image->path());
                $image->resize($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$image_name_o_original,90);

                $images_path[] = $image_name_o_original;
            }
            $input['images'] = $images_path;
        }


        $brand->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Brand has been updated successfully']);
        } else {
            return redirect()->route('admin.brands.index')->with(['success' => 'brand has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $brand = Brand::where('user_id', $id)->first();

        $all_slug = AllSlug::where('causer_id',$brand->id)->where('causer_type', 'App\Models\Brand')->first();
        $all_slug->delete();

        $this->destroy_image($brand->image);
        if(!empty($brand->images)) {
            foreach ($brand->images as $brand_image) {
                $this->destroy_image($brand_image);
            }
        }

        $user->delete();
        return response()->json(['success' => 'Brand has been deleted successfully']);
    }

    public function destroy_image($image)
    {
        if(!empty($image) && $image != null){
            // Storage::delete('public/' . $image);

        }
        Storage::disk('public')->delete(Brand::FILE_DIRECTORY . '/'. $image);
        return 1;
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';

        $user = User::findOrFail($id);
        if($user) {
            $user->is_active = $changeStatus;
            $user->save();
        }

        $model = Brand::where('user_id', $user->id)->first();
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public  function product_index() {

        return view('admin.pages.brands.index-product');
    }
    public  function product_data() {
//      $products = Product::select(['id','name','image','is_active','created_at'])->where('user_id',Auth::user()->id); //->latest()
        $brand = Brand::where('user_id',Auth::user()->id)->first();
        $products = Product::whereHas('brands' , function($q) use($brand) {
            $q->where('brands.id', $brand->id);
        })->select(['id','name','image','is_active','created_at'])->latest();

        return DataTables::of($products)
            /*return Datatables::eloquent(Product::select())*/
            ->rawColumns(['name','action'])
            ->editColumn('name', function ($modal) {
                $main_image = !empty($modal->image) ? '<img src="' . asset($modal->image) . '" alt="" height="50" />' : '';
                return '<p>' . $modal->name . " " . $main_image . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.brands.products.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.brands.products.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.brands.products.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('product_{{$id}}')
            ->make(true);
    }
    public function product_create() {
        $product_types = ProductType::pluck('name','id');
        return view('admin.pages.brands.add-product',['product_types'=> $product_types]);
    }

    public function product_store(Request $request) {

        $validated = request()->validate([
            'name' => 'required',
            'unit_om' => 'required',
            'tax' => 'required',
        ]);
        $product = new Product;
        $input = $request->only(['name','description','video','unit_om','hsn_code','tax','delivery_unit','site_title','site_keywords','site_description']);
        $input['uuid'] = Str::uuid();
        $input['user_id'] = Auth::user()->id;
        $input['is_active'] = 1;
        $input['is_featured'] = isset($request->is_featured) ? 1 : 0;
        $input['is_home'] = isset($request->is_home) ? 1 : 0;
        $product->fill($input)->save();

        $slug = new Slug();
        $slug_data = $slug->createSlug($request->name);

        $all_slug = new AllSlug();
        $all_slug->fill([
            'causer_id' => $product->id,
            'causer_type' => 'App\Models\Product',
            'slug' => $slug_data,
        ]);
        $all_slug->save();


        $imgUploadSize = Utility::IMAGE_PRODUCT_THUMB;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        $imgUploadSize_b = Utility::IMAGE_PRODUCT;
        $imgUploadSizeW_b = Utility::getImageDimension($imgUploadSize_b)['width'];
        $imgUploadSizeH_b = Utility::getImageDimension($imgUploadSize_b)['height'];

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $file_name = Utility::addUnderScore($product->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $file_name_b = Utility::IMAGE_PRODUCT . '_' . Utility::addUnderScore($product->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Product::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

                $image = ResizeImage::make($file->path());
                $image->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name,90);

                $image->resize($imgUploadSizeW_b, $imgUploadSizeH_b, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name_b,90);

            $product->image = $file_name;
            $product->save();
        }

        if($request->hasFile('images')) {
            $images = $request->file('images');

            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Product::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

            $images_path = [];
            foreach($images as $index => $image) {
                $image_name = Utility::addUnderScore($product->id) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .
                $image_name_o_original = Utility::IMAGE_PRODUCT . '_' . Utility::addUnderScore($product->id) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .

                $image_m = ResizeImage::make($image->path());

                $image_m->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image_m->save($destinationPath.'/'.$image_name,90);

                $image_m->resize($imgUploadSizeW_b, $imgUploadSizeH_b, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image_m->save($destinationPath.'/'.$image_name_o_original,90);

                $images_path[] = $image_name;
            }
            $product->images = $images_path;
        }

        if(request()->hasFile('brochure')) {
            $fileBrochure = $request->file('brochure');
            // $extension = request('brochure')->extension();
            $fileNameBrochure = Utility::addUnderScore($product->id) . Utility::cleanString($fileBrochure->getClientOriginalName());
            request('brochure')->storeAs('public/image_brochures', $fileNameBrochure);
            $product->brochure = $fileNameBrochure;
        }

        $product->uuid = $product->id.$product->uuid;
        $product->save();


        foreach($request->type_size as $index=>$type_size) {

            if(!empty($type_size)) {
                $typeProductPivot = new TypeProductPivot();
                $typeProductPivot->fill(['type_id' => $type_size, 'product_id' => $product->id, 'mrp' => $request->mrp[$index], 'stock' => $request->stock[$index]])->save();
                foreach ($request->quantity_from[$index] as $index2 => $quantity_from) {
                    if (!empty($quantity_from)) {
                        $quantity_to = $request->quantity_to[$index][$index2];
                        $price = $request->price[$index][$index2];

                        $PriceDetail = new PriceDetail();
                        $PriceDetail->create(['tp_pivot_id' => $typeProductPivot->id, 'quantity_from' => $quantity_from, 'quantity_to' => $quantity_to, 'price' => $price]);
                    }
                }
            }
        }

        $product_single = Product::find($product->id);
        if($request->has('category')) {
            $product_single->categories()->attach($request->category);
        }

        DB::table('brand_product')->insert(
            ['brand_id' => Auth::user()->brand->id, 'product_id' => $product->id]
        );


        if ($request->ajax()) {
            return response()->json(['success' => 'New product has been added successfully']);
        } else {
            return redirect()->route('admin.brands.products.index')->with('success', 'New product has been added successfully');
        }
    }
    public function product_edit($id) {
        $product_types = ProductType::pluck('name','id');
        $product = Product::findOrFail($id);
        $type_sizes = TypeProductPivot::where('product_id',$id)->distinct('type_id')->get();
        foreach($type_sizes as $key=>$type_size) {
            $quantities = PriceDetail::where('tp_pivot_id',$type_size->id)->get(['quantity_from','quantity_to','price']);
            $type_size->quantities = $quantities;
        }

        $brands = Brand::pluck('name','id');
        $brand_id_array = [];
        foreach($product->brands as $item) {
            $brand_id_array[] = $item['id'];
        }

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Product')->first();
        if($all_slug) {
            $slug = $all_slug->slug;
        }else {
            $slug = null;
        }

        return view('admin.pages.brands.add-product',['product'=>$product,'type_sizes'=>$type_sizes,'product_types'=>$product_types,'brands'=> $brands, 'brand_id_array'=> $brand_id_array, 'slug'=>$slug]);
    }
    public function product_update(Request $request, $id) {
        $validated = request()->validate([
            'name' => 'required',
            'unit_om' => 'required',
            'tax' => 'required',
        ]);

        $product = Product::find($id);
        $input = $request->only(['name','description','video','unit_om','hsn_code','tax','site_title','site_keywords','site_description']);
        $input['is_featured'] = isset($request->is_featured) ? 1 : 0;
        $input['is_home'] = isset($request->is_home) ? 1 : 0;
        $input['delivery_unit'] = empty($request['delivery_unit'])? 0 : $request['delivery_unit'];
        $input['delivery_min'] = empty($request['delivery_min'])? 0 : $request['delivery_min'];
        $input['delivery_max'] = empty($request['delivery_max'])? 0 : $request['delivery_max'];

        $slug = new Slug();
        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Product')->first();
        if($all_slug) {
            if ($product->name != $request->name) {
                $slug_data = $slug->createSlug($request->name, $id);
                $all_slug->fill(['slug' => $slug_data])->save();
            }
        }else {
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $product->id,
                'causer_type' => 'App\Models\Product',
                'slug' => $slug_data,
            ]);
            $all_slug->save();
        }

        $product->fill($input)->save();

        $imgUploadSize = Utility::IMAGE_PRODUCT_THUMB;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        $imgUploadSize_b = Utility::IMAGE_PRODUCT;
        $imgUploadSizeW_b = Utility::getImageDimension($imgUploadSize_b)['width'];
        $imgUploadSizeH_b = Utility::getImageDimension($imgUploadSize_b)['height'];

        if($request->hasFile('image')) {
            $this->destroy_image($product->image);
            $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product->image);
            $file = $request->file('image');
            $file_name = Utility::addUnderScore($product->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $file_name_b = Utility::IMAGE_PRODUCT . '_' . Utility::addUnderScore($product->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Product::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

                $image = ResizeImage::make($file->path());
                $image->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name,90);

                $image->resize($imgUploadSizeW_b, $imgUploadSizeH_b, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name_b,90);

            $product->image = $file_name;
            $product->save();
        }
        // else {
        //     if(isset($request->is_image) && ($request->is_image == 0)){
        //         $this->destroy_image($product->image);
        //         $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product->image);
        //         $input['image'] = null;
        //     }
        // }

        if(isset($request->is_images) && ($request->is_images == 0)){
            if(!empty($product->images)) {
                foreach ($product->images as $product_image) {
                    $this->destroy_image($product_image);
                    $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product_image);
                }
            }
            $input['images'] = null;
        }

        if($request->hasFile('images')) {
            // if(!empty($product->images)) {
            //     foreach ($product->images as $product_image) {
            //         $this->destroy_image($product_image);
            //         $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product_image);
            //     }
            // }

            $images = $request->file('images');

            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Product::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

            $images_path = [];
            foreach($images as $index => $image) {

                $image_name = Utility::addUnderScore($product->id) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .
                $image_name_o_original = Utility::IMAGE_PRODUCT . '_' . Utility::addUnderScore($product->id) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .

                $image_m = ResizeImage::make($image->path());

                $image_m->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image_m->save($destinationPath.'/'.$image_name,90);

                $image_m->resize($imgUploadSizeW_b, $imgUploadSizeH_b, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image_m->save($destinationPath.'/'.$image_name_o_original,90);

                $images_path[] = $image_name;
            }
            $input['images'] = $images_path;
        }

        if(request('is_brochure')==0) {
            Storage::disk('public')->delete(Product::FILE_DIRECTORY_BROCHURE . '/'. $product->brochure);
            $product->brochure =  null;
        }

        if(request()->hasFile('brochure')) {
            $fileBrochure = $request->file('brochure');
            // $extension = request('brochure')->extension();
            $fileNameBrochure = Utility::addUnderScore($product->id) . Utility::cleanString($fileBrochure->getClientOriginalName());
            request('brochure')->storeAs('public/image_brochures', $fileNameBrochure);
            $product->brochure = $fileNameBrochure;
        }


        foreach($product->product_types as $prod_types) {
            $size = ProductType::find($prod_types->id);
            $detach = $size->products()->detach($product->id);
        }


        foreach($request->type_size as $index=>$type_size) {
            if(!empty($type_size)) {
                $typeProductPivot = new TypeProductPivot();
                $typeProductPivot->fill(['type_id' => $type_size, 'product_id' => $product->id, 'mrp' => $request->mrp[$index], 'stock' => $request->stock[$index]])->save();
                foreach ($request->quantity_from[$index] as $index2 => $quantity_from) {
                    if (!empty($quantity_from)) {
                        $quantity_to = $request->quantity_to[$index][$index2];
                        $price = $request->price[$index][$index2];

                        $PriceDetail = new PriceDetail();
                        $PriceDetail->create(['tp_pivot_id' => $typeProductPivot->id, 'quantity_from' => $quantity_from, 'quantity_to' => $quantity_to, 'price' => $price]);
                    }
                }
            }
        }

        $product->fill($input)->save();

        $product_single = Product::find($product->id);
        $product_single->categories()->sync($request->category);

        $product_single->brands()->sync($request->brands);

        if ($request->ajax()) {
            return response()->json(['success' => 'Product has been updated successfully']);
        } else {
            return redirect()->route('admin.products.index')->with('success', 'Product has been updated successfully');
        }
    }

    public function product_destroy($id)
    {
        $product = Product::find($id);

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Product')->first();
        $all_slug->delete();

        if(!empty($product->image) && $product->image != null){
            Storage::disk('public')->delete(Product::FILE_DIRECTORY . '/'. $product->image);
            $bigImage = str_replace(Product::FILE_DIRECTORY."/",Product::FILE_DIRECTORY."/".Utility::KHM_BIG_IMAGE_SIZE.'_',$product->image);
            Storage::disk('public')->delete(Product::FILE_DIRECTORY . '/'. $bigImage);

        }
        $product->delete();

        return response()->json(['success' => 'Product has been deleted successfully']);
    }

    public function product_change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Product::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public function sidebar_categories($product_id='')
    {
        $product_categories = [];
        if($product_id !='') {
            $product = Product::find($product_id);
            $product_category_array = $product->categories;
            foreach ($product_category_array as $product_category) {
                $product_categories[] = $product_category->id;
            }
        }else {
            $product_categories = [];
        }

        $categories = Category::has('parents','0')->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->where('is_active',Utility::STATUS_ACTIVE)->orderBy('order_no','asc')->get();
        $content = '<ul class="list-group">';

        $offerCatName = Category::where('id',Utility::CATEGORY_ID_OFFER)->value('name');
        $isChecked_offer = in_array(Utility::CATEGORY_ID_OFFER, $product_categories) ? 'checked' : '';

        $content .= '<li class="list-group-item">
                            <div class="checkbox">
                            <input type="checkbox" name="category[]" id="category_' . Utility::CATEGORY_ID_OFFER . '" value="' . Utility::CATEGORY_ID_OFFER . '" ' . $isChecked_offer . ' />
                            <label for="category_' . Utility::CATEGORY_ID_OFFER . '">
                                    <strong>' . $offerCatName . '</strong>
                                </label></div>
                        </li>';

        foreach ($categories as $category) {
            $content .= $this->show_category_lists($category,$product_categories);
        }
        $content .= '</ul>';

        return ['content'=>$content];
    }
    public  function show_category_lists($category,$product_categories) {



        /*$child_categories = Category::where('parent',$category->id)->get();*/
        // $child_categories = $category->childs;

        // $child_content = '';
        // if($child_categories) {
        //     $child_content .= '<div style="padding-left:20px;margin-top:10px;">
        //                             <ul class="list-group">';
        //     foreach ($child_categories as $child_category) {
        //         $isChecked = in_array($child_category->id, $product_categories) ? 'checked' : '';
        //         $child_content .= '<li class="list-group-item">
        //                                     <input type="checkbox" name="category[]" id="child_category_' . $child_category->id . '" value="' . $child_category->id . '" ' . $isChecked . '   />
        //                                     <label for="child_category_' . $child_category->id . '">
        //                                         ' . $child_category->name . '
        //                                     </label>
        //                                 </li>';
        //     }
        //     $child_content .= '</ul>
        //                         </div>';
        // }

        $content = '<li class="list-group-item">
                            <div class="checkbox">';
        //if($category->id == Utility::CATEGORY_ID_OFFER) {
            $isChecked2 = in_array($category->id, $product_categories) ? 'checked' : '';
            $content .= '<input type="checkbox" name="category[]" id="category_' . $category->id . '" value="' . $category->id . '" ' . $isChecked2 . ' />';
        // }
        $content .= '<label for="category_' . $category->id . '">
                                    <strong>' . $category->name . '</strong>
                                </label>' .
                                // $child_content .
            '</div>
                        </li>';
        return $content;
    }

    public function show_meta() {
        $id = Auth::id();
        $user = User::findOrFail($id);
        return view('admin.pages.brands.add-meta',['user'=>$user]);
    }

    public function update_meta(Request $request) {
        $id = Auth::id();
        $brand = Brand::where('user_id', $id)->first();

        $input = $request->only(['site_title','site_keywords','site_description']);
        $brand->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Meta Details have been added successfully']);
        } else {
            return redirect()->route('admin.brands.show.meta')->with('success', 'Meta Details have been added successfully');
        }
    }

    public  function affiliates() {
        return view('admin.pages.brands.affiliates');
    }
    public  function affiliates_data() {
        $affiliates = User::whereHas('roles' , function($q){
            $q->where('id', Utility::AFFILIATE_ROLE_ID);
        })->select()->latest();
        return DataTables::of($affiliates)
            ->rawColumns(['name','email','action'])
            ->editColumn('name', function ($modal) {
                $all_slug = AllSlug::where('causer_id',$modal->affiliate->id)->where('causer_type', 'App\Models\Affiliate')->first();
                $slug = $all_slug->slug;
                $data = '<p>' . $modal->name . '</p>';
                return $data;
            })
            ->editColumn('email', function ($modal) {
                return '<p>' . $modal->email . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $is_dealer = $modal->brands->where('user_id',Auth::id())->first();
                $publishIcon = $is_dealer ? 'fa fa-check' : 'fa fa-plus';
                $publishTitle = $is_dealer ? 'Remove Dealer' : 'Add as Dealer';
                return '<a  href="'. route('admin.brands.dealer.add',[$modal->id]) . '" title="' . $publishTitle . '" > <i class="' . $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('affiliate_{{$id}}')
            ->make(true);
    }

    public  function affiliate_add($id, Request $request) {
        $dealer = User::find($id);
        $is_dealer = $dealer->brands->where('user_id',Auth::id())->first();

        $user = User::find(Auth::id());
        $brand = $user->brand;

        if($is_dealer) {
            $brand->users()->detach($id);
            $status = 'removed';
        }else{
            $brand->users()->attach($id);
            $status = 'added';
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Dealer has been ' . $status . ' to successfully']);
        } else {
            return redirect()->route('admin.brands.dealer.index')->with('success', 'Dealer has been ' . $status . ' to successfully');
        }
    }
}
