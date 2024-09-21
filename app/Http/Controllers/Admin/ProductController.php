<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceDetail;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Services\Slug;
use App\Models\TypeProductPivot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Uuid;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Image;
use App\Http\Utilities\Utility;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ResizeImage;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public  function index() {

        return view('admin.pages.products.index');
    }
    public  function data() {
        $products = Product::select(['id','name','image','is_active','created_at']); //->latest()
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
                return '<a  href="'. route('admin.products.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.products.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.products.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('product_{{$id}}')
            ->make(true);
    }
    public function create() {
        $brands = Brand::pluck('name','id');
        $product_types = ProductType::pluck('name','id');
        return view('admin.pages.products.add',['product_types'=> $product_types,'brands'=> $brands]);
    }

    public function store(Request $request) {
        /*return $request->all();*/
        // $rules = [
        //     'name' => 'required',
        //     'unit_om' => 'required',
        //     'tax' => 'required',
        // ];
        // $messages = [
        //     'required' => 'The :attribute field is required.',
        // ];
        // $validator = Validator::make($request->all(), $rules, $messages);
        // if ($validator->fails()) {
        //     if($request->ajax()) {
        //         return response()->json($validator->errors(), 422);
        //     } else {
        //         return redirect()->route('admin.products.create')
        //             ->withErrors($validator)
        //             ->withInput($request->except('password','password_confirm'));
        //     }
        // }
        // else {


        // }

        $validated = request()->validate([
            'name' => 'required',
            'unit_om' => 'required',
            'tax' => 'required',
        ]);

        $product = new Product;
        $input = $request->only(['name','description','video','unit_om','hsn_code','tax','site_title','site_keywords','site_description']);
        // $input['uuid'] = Uuid::generate();
        $input['uuid'] = Str::uuid();

        $input['user_id'] = Auth::user()->id;
        $input['is_active'] = 1;
        $input['delivery_unit'] = empty($request['delivery_unit'])? 0 : $request['delivery_unit'];
        $input['delivery_min'] = empty($request['delivery_min'])? 0 : $request['delivery_min'];
        $input['delivery_max'] = empty($request['delivery_max'])? 0 : $request['delivery_max'];
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
            $file_name = Utility::addUnderScore($product->id) . Utility::cleanString($file->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .
            $file_name_b = Utility::IMAGE_PRODUCT . '_' . Utility::addUnderScore($product->id) . Utility::cleanString($file->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .
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

        if($request->has('brands')) {
            $product_single->brands()->attach($request->brands);
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'New product has been added successfully']);
        } else {
            return redirect()->route('admin.products.index')->with('success', 'New product has been added successfully');
        }
    }
    public function edit($id) {
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

        return view('admin.pages.products.add',['product'=>$product,'type_sizes'=>$type_sizes,'product_types'=>$product_types,'brands'=> $brands, 'brand_id_array'=> $brand_id_array, 'slug'=>$slug]);
    }
    public function update(Request $request, $id) {

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

        // $product->fill($input)->save();

        $imgUploadSize = Utility::IMAGE_PRODUCT_THUMB;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        $imgUploadSize_b = Utility::IMAGE_PRODUCT;
        $imgUploadSizeW_b = Utility::getImageDimension($imgUploadSize_b)['width'];
        $imgUploadSizeH_b = Utility::getImageDimension($imgUploadSize_b)['height'];

        if(isset($request->is_image) && ($request->is_image == 0)){
            $this->destroy_image($product->image);
            $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product->image);
            $input['image'] = null;
        }
        if($request->hasFile('image')) {
            // $this->destroy_image($product->image);
            // $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product->image);
            $file = $request->file('image');
            $file_name = Utility::addUnderScore($product->id) . Utility::cleanString($file->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .
            $file_name_b = Utility::IMAGE_PRODUCT . '_' . Utility::addUnderScore($product->id) . Utility::cleanString($file->getClientOriginalName()); //Utility::addUnderScore($imgUploadSize) .
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

                $input['image'] = $file_name;
            // $product->save();
        }
        // else {

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

    public function destroy($id)
    {
        $product = Product::find($id);

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Product')->first();
        $all_slug->delete();

        if(!empty($product->image) && $product->image != null){
            // Storage::delete('public/' . $product->image);
            $this->destroy_image($product->image);
            $this->destroy_image(Utility::IMAGE_PRODUCT.'_' . $product->image);
        }
        $product->delete();

        return response()->json(['success' => 'Product has been deleted successfully']);
    }

    public function destroy_image($image)
    {
        Storage::disk('public')->delete(Product::FILE_DIRECTORY . '/'. $image);
        return 1;
    }

    public function change_status(Request $request, $id)
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

        $categories = Category::whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->where('is_active',Utility::STATUS_ACTIVE)->orderBy('order_no','asc')->get();
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
        // if($category->id != Utility::CATEGORY_ID_OFFER) {
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

}
