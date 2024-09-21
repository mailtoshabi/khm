<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\Category;
use App\Models\Services\Slug;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
// use Validator;
// use File;
use Image;
use App\Http\Utilities\Utility;
use Intervention\Image\Facades\Image as ResizeImage;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public  function index() {
        return view('admin.pages.categories.index');
    }
    public  function data() {

        return Datatables::eloquent(Category::select()) //->oldest()
            ->rawColumns(['name','action']) //'parent',
            ->editColumn('name', function ($modal) {
                $data = '<p>' . $modal->name . '</p>';
                $all_slug = AllSlug::where('causer_id',$modal->id)->where('causer_type', 'App\Models\Category')->first();
                if(!empty($all_slug)) {
                    $slug = $all_slug->slug;
                    $data .= '<a href="'. config('app.website_url') . '/' . $slug . '" target="_blank">'. config('app.website_url') . '/' . $slug . '</a>';
                }
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
            // ->addColumn('parent', function ($modal) {
            //     $parent_names = [];
            //     if(!empty($modal->parents)) {
            //         foreach($modal->parents as $parent) {
            //             $parent_names[] = $parent->name;
            //         }
            //     }
            //     return implode(", ",$parent_names);
            // })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-category" data-target="' . route('admin.categories.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.categories.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.categories.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('category_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $categoryLists = Category::where('parent','0')->get();
        $returnHTML = view('admin.pages.categories.add-modal-form',['categoryLists' => $categoryLists])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public  function store(Request $request) {
        // return 'shabeer';
        // return  $request->all();
        // $rules = [
        //     'name' => 'required|max:255|unique:categories',
        // ];
        // $messages = [
        //     'required' => 'The :attribute field is required.',
        // ];
        // $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     if($request->ajax()) {
        //         return response()->json($validator->errors(), 422);
        //     } else {
        //         /*return redirect()->route('admin.lab.customers.create')
        //             ->withErrors($validator)
        //             ->withInput($request->all());*/
        //     }
        // }

        $validated = request()->validate([
            'name' => 'required|max:255|unique:categories',
        ]);


        $category = new Category;
        $input = $request->only(['name','site_title','site_keywords','site_description']);
        /*$input['parent'] = empty($request->parent) ? 0 : $request->parent;*/
        // $input['parent'] = 0;
        $input['order_no'] = empty($request->order_no) ? Utility::DEFAULT_DB_ORDER : $request->order_no;
        $category->fill($input)->save();

        // if($request->has('parent')) {
        //     $parents = Category::find($category->id);
        //     $parents->parents()->attach($request->parent);
        // }

        $slug = new Slug();
        $slug_data = $slug->createSlug($request->name);

        $all_slug = new AllSlug();
        $all_slug->fill([
            'causer_id' => $category->id,
            'causer_type' => 'App\Models\Category',
            'slug' => $slug_data,
        ]);
        $all_slug->save();

        $imgUploadSize = Utility::IMAGE_CATEGORY;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $file_name = Utility::addUnderScore($category->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Category::FILE_DIRECTORY);

            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

                $image = ResizeImage::make($file->path());
                $image->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name,90);

                $category->image = $file_name;
                $category->save();
            }

            // if(request()->hasFile('image')) {
            //     $extension = request('image')->extension();
            //     $fileName = 'project_pic_' . date('YmdHis') . '.' . $extension;
            //     request('image')->storeAs('public/dir_categories', $fileName);
            //     $category->image = $fileName;
            //     $category->save();

            //     // $path = $request->file('image')->store('dir_categories');

            //     // $path = Storage::putFileAs(
            //     //     'public/dir_categories', $request->file('image'), 'sdasd'
            //     // );
            // }


        if ($request->ajax()) {
            return response()->json(['customer'=>$category, 'success' => 'New Category added successfully']);
        } else {
            return redirect()->route('admin.categories.index')->with(['success' => 'New Category added successfully']);
        }
    }

    public function edit($id)
    {
        /*$category = Category::findOrFail($id);
        return response()->json(['category' => $category]);*/
    }

    public function edit_modal($id)
    {
        $category = Category::findOrFail($id);
        /*$categoryLists = Category::where('parent','0')->where('id','!=',$id)->get();*/

        // $categoryLists = Category::has('parents',0)->get();

        // $parent_ids = [];
        // if(!empty($category->parents)) {
        //     foreach($category->parents as $parent) {
        //         $parent_ids[] = $parent->id;
        //     }
        // }
        // $category->parent_ids = $parent_ids;

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Category')->first();
        if($all_slug) {
            $slug = $all_slug->slug;
        }else {
            $slug = null;
        }

        $returnHTML = view('admin.pages.categories.add-modal-form',['category' => $category, 'slug'=>$slug])->render(); //'categoryLists' => $categoryLists,
        return response()->json(['html' => $returnHTML]);
    }

    public function update(Request $request, $id)
    {
        // $rules = [
        //     'name' => 'required|max:255|unique:categories,name,'.$id,
        // ];
        // $messages = [
        //     'required' => 'The :attribute field is required.',
        // ];
        // $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     if($request->ajax()) {
        //         return response()->json($validator->errors(), 422);
        //     } else {
        //         return redirect()->route('admin.categories.edit', ['id' => $id])
        //             ->withErrors($validator)
        //             ->withInput($request->all());
        //     }
        // }

        $validated = request()->validate([
            'name' => 'required|max:255|unique:categories,name,'.$id,
        ]);

        $category = Category::find($id);
        $input = $request->only(['name','site_title','site_keywords','site_description']);
        // $input['parent'] = 0 ;
        $input['order_no'] = empty($request->order_no) ? Utility::DEFAULT_DB_ORDER : $request->order_no;

        /*if($request->has('parent')) {*/
            // $parents = Category::find($category->id);
            // $parents->parents()->sync($request->parent);
        /*}*/

        $slug = new Slug();
        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Category')->first();
        if($all_slug) {
            if ($category->name != $request->name) {
                $slug_data = $slug->createSlug($request->name, $id);
                $all_slug->fill(['slug' => $slug_data])->save();
            }
        }else {
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $category->id,
                'causer_type' => 'App\Models\Category',
                'slug' => $slug_data,
            ]);
            $all_slug->save();
        }




        $imgUploadSize = Utility::IMAGE_CATEGORY;
        $imgUploadSizeW = Utility::getImageDimension($imgUploadSize)['width'];
        $imgUploadSizeH = Utility::getImageDimension($imgUploadSize)['height'];

        if($request->hasFile('image')) {
            $this->destroy_image($category->image);
            $file = $request->file('image');
            $file_name = Utility::addUnderScore($category->id) . Utility::addUnderScore($imgUploadSize) . Utility::cleanString($file->getClientOriginalName());
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Category::FILE_DIRECTORY);

            // $path = public_path('images/');
            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);

                $image = ResizeImage::make($file->path());
                $image->resize($imgUploadSizeW, $imgUploadSizeH, function($constraint) {
                    // $constraint->aspectRatio();
                });
                $image->save($destinationPath.'/'.$file_name,90);

            // $category->image = $file_name;
            $input['image'] = $file_name;
        }
        else {
                if(isset($request->is_image) && ($request->is_image == 0)){
                    $this->destroy_image($category->image);
                    $input['image'] = null;
                }
            }





        // if($request->hasFile('image')) {
        //     if(!empty($category->image) && $category->image != null){
        //         $this->destroy_image($category->image);
        //         // Storage::delete('public/' . $category->image);
        //         $bigImage = str_replace(Category::FILE_DIRECTORY."/",Category::FILE_DIRECTORY."/".Utility::KHM_BIG_IMAGE_SIZE.'_',$category->image);
        //         // Storage::delete('public/' . $bigImage);
        //         $this->destroy_image($bigImage);

        //     }
        //     $image = $request->file('image');
        //     $image_name = $category->id . '_' . str_replace(' ','_',$image->getClientOriginalName());
        //     $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Category::FILE_DIRECTORY);
        //     if (!File::exists($destinationPath)) {
        //         File::makeDirectory($destinationPath, $mode = 0777, true, true);
        //     }
        //     $realPath = $_FILES['image']['tmp_name'];
        //     $contents = file_get_contents($realPath);
        //     $contents = \Image::make($contents);
        //     $image_path = Category::FILE_DIRECTORY . '/'. $image_name;
        //     $contents->resize(206, 224, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        //     Storage::disk('prod_cats')->put($image_name, $contents->encode());
        //     $input['image'] = $image_path;
        // }
        // else {
        //     if(isset($request->is_image) && ($request->is_image == 0)){
        //         Storage::delete('public/' . $category->image);
        //         $bigImage = str_replace(Category::FILE_DIRECTORY."/",Category::FILE_DIRECTORY."/".Utility::KHM_BIG_IMAGE_SIZE.'_',$category->image);
        //         Storage::delete('public/' . $bigImage);
        //         $input['image'] = null;
        //     }
        // }
        $category->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Category has been updated successfully']);
        } else {
            return redirect()->route('admin.categories.index')->with(['success' => 'category has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Category')->first();
        $all_slug->delete();

        $category = Category::find($id);
        $this->destroy_image($category->image);
        // if(!empty($category->image) && $category->image != null){
        //     Storage::delete('public/' . $category->image);
        //     $bigImage = str_replace(Category::FILE_DIRECTORY."/",Category::FILE_DIRECTORY."/".Utility::KHM_BIG_IMAGE_SIZE.'_',$category->image);
        //     Storage::delete('public/' . $bigImage);
        // }
        $category->delete();
        return response()->json(['success' => 'Category has been deleted successfully']);
    }

    public function destroy_image($image)
    {
        if(!empty($image) && $image != null){
            // Storage::delete('public/' . $image);
            Storage::disk('public')->delete(Category::FILE_DIRECTORY . '/'. $image);
        }
        return 1;
    }

    public function change_status(Request $request, $id)
    {

        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Category::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
