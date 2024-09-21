<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\Category;
use App\Models\Pin;
use App\Models\Services\Slug;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use App\Http\Utilities\Utility;

class StoreController extends Controller
{
    public  function index() {
        return view('admin.pages.stores.index');
    }
    public  function data() {
        $stores = Store::select()->latest();
        return DataTables::of($stores)
            ->rawColumns(['name','city','pins','action'])
            ->editColumn('name', function ($modal) {
                $all_slug = AllSlug::where('causer_id',$modal->id)->where('causer_type', 'App\Models\Store')->first();
                $slug = $all_slug->slug;
                $data = '<p>' . $modal->name . '</p>';
                $data .= '<a href="'. config('app.website_url') . '/' . $slug . '" target="_blank">'. config('app.website_url') . '/' . $slug . '</a>';
                return $data;
            })
            ->editColumn('city', function ($modal) {
                return '<p>' . $modal->city . '</p>';
            })
            ->editColumn('pins', function ($modal) {
                $pins = [];
                if(!empty($modal->pins)) {
                    foreach($modal->pins as $pin) {
                        $pins[] = $pin->name;
                    }
                }
                if(!empty($pins)) {
                    $data = implode(", ",$pins);
                }else {
                    $data = '';
                }
                return '<p>' . $data . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.stores.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.stores.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.stores.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('store_{{$id}}')
            ->make(true);
    }
    public function create() {
        $pinLists = Pin::pluck('name','id');
        return view('admin.pages.stores.add',['pinLists'=> $pinLists]);
    }

    public function store(Request $request) {
        /*return $request->all();*/
        $rules = [
            'name' => 'required',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.stores.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $store = new Store;
            $input = $request->only(['name','short_description','description','footer_description','email','phone','location','city','district','site_title','site_keywords','site_description']);
            $input['is_active'] = 1;
            $store->fill($input)->save();

            $slug = new Slug();
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $store->id,
                'causer_type' => 'App\Models\Store',
                'slug' => $slug_data,
            ]);
            $all_slug->save();

            if($request->hasFile('image')) {
                $image = $request->file('image');
                $image_name = $store->id . '_' . str_replace(' ','_',$image->getClientOriginalName());
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Store::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image']['tmp_name'];
                $contents = file_get_contents($realPath);
                $contents = \Image::make($contents);
                $image_path = Store::FILE_DIRECTORY . '/'. $image_name;

                /*$contents->fit(120, 25, function ($constraint) {
                    $constraint->aspectRatio();
                });*/

                Storage::disk('stores')->put($image_name, $contents->encode());
                $store->image = $image_path;
            }

            if($request->hasFile('brochure')) {
                $brochure = $request->file('brochure');
                $brochure_name = $store->id . '_' . str_replace(' ','_',$brochure->getClientOriginalName());
                $destinationPath_bro = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Store::FILE_DIRECTORY_BROCHURE);
                if (!File::exists($destinationPath_bro)) {
                    File::makeDirectory($destinationPath_bro, $mode = 0777, true, true);
                }
                $realPath = $_FILES['brochure']['tmp_name'];
                $contents_bro = file_get_contents($realPath);

                /*IMAGE CROP START*/
                $contents_bro = \Image::make($contents_bro);
                $brochure_path = Store::FILE_DIRECTORY_BROCHURE . '/'. $brochure_name;
                $contents_bro->fit(340, 200, function ($constraint) {
                    $constraint->aspectRatio();
                });
                Storage::disk('store_brochures')->put($brochure_name, $contents_bro->encode());
                $store->brochure = $brochure_path;
                /*IMAGE CROP END*/

                /*$brochure_path = Store::FILE_DIRECTORY_BROCHURE . '/'. $brochure_name;
                Storage::disk('store_brochures')->put($brochure_name, $contents_bro);
                $store->brochure = $brochure_path;*/
            }


            $store->save();

            if($request->has('pins')) {
                $store_single = Store::find($store->id);
                $store_single->pins()->attach($request->pins);
            }

            if($request->has('category')) {
                $categories = Store::find($store->id);
                $categories->categories()->attach($request->category);
            }

            if ($request->ajax()) {
                return response()->json(['success' => 'New store has been added successfully']);
            } else {
                return redirect()->route('admin.stores.index')->with('success', 'New store has been added successfully');
            }
        }
    }
    public function edit($id) {

        $pinLists = Pin::pluck('name','id');
        $store = Store::findOrFail($id);

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Store')->first();
        if($all_slug) {
            $slug = $all_slug->slug;
        }else {
            $slug = null;
        }

        $pin_id_array = [];
        foreach($store->pins as $item) {
            $pin_id_array[] = $item['id'];
        }

        return view('admin.pages.stores.add',['store'=>$store,'pinLists'=> $pinLists, 'pin_id_array'=> $pin_id_array, 'slug'=>$slug]);
    }
    public function update(Request $request, $id) {
        $rules = [
            'name' => 'required',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.stores.edit',$id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $store = Store::find($id);
            $input = $request->only(['name','short_description','description','footer_description','email','phone','location','city','district','site_title','site_keywords','site_description']);


            $slug = new Slug();
            $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Store')->first();
            if($all_slug) {
                if ($store->name != $request->name) {
                    $slug_data = $slug->createSlug($request->name, $id);
                    $all_slug->fill(['slug' => $slug_data])->save();
                }
            }else {
                $slug_data = $slug->createSlug($request->name);

                $all_slug = new AllSlug();
                $all_slug->fill([
                    'causer_id' => $store->id,
                    'causer_type' => 'App\Models\Store',
                    'slug' => $slug_data,
                ]);
                $all_slug->save();
            }

            $store->fill($input)->save();

            if($request->hasFile('image')) {
                if(!empty($store->image) && $store->image != null){
                    Storage::delete('public/' . $store->image);
                }
                $image = $request->file('image');
                $image_name = $store->id . '_' . str_replace(' ','_',$image->getClientOriginalName());
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Store::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image']['tmp_name'];
                $contents = file_get_contents($realPath);
                $contents = \Image::make($contents);
                $image_path = Store::FILE_DIRECTORY . '/'. $image_name;
                /*$contents->fit(550, 600, function ($constraint) {
                    $constraint->aspectRatio();
                });*/
                Storage::disk('stores')->put($image_name, $contents->encode());
                $input['image'] = $image_path;
            }
            else {
                if(isset($request->is_image) && ($request->is_image == 0)){
                    Storage::delete('public/' . $store->image);
                    $input['image'] = null;
                }
            }
            if($request->hasFile('brochure')) {
                if(!empty($store->brochure) && $store->brochure != null){
                    Storage::delete('public/' . $store->brochure);
                }
                $brochure = $request->file('brochure');
                $brochure_name = $store->id . '_' . str_replace(' ','_',$brochure->getClientOriginalName());
                $destinationPath_bro = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Store::FILE_DIRECTORY_BROCHURE);
                if (!File::exists($destinationPath_bro)) {
                    File::makeDirectory($destinationPath_bro, $mode = 0777, true, true);
                }
                $realPath = $_FILES['brochure']['tmp_name'];
                $contents_bro = file_get_contents($realPath);


                /*IMAGE CROP START*/
                $contents_bro = \Image::make($contents_bro);
                $brochure_path = Store::FILE_DIRECTORY_BROCHURE . '/'. $brochure_name;
                $contents_bro->fit(340, 200, function ($constraint) {
                    $constraint->aspectRatio();
                });
                Storage::disk('store_brochures')->put($brochure_name, $contents_bro->encode());
                $store->brochure = $brochure_path;
                /*IMAGE CROP END*/


                /*$brochure_path = Store::FILE_DIRECTORY_BROCHURE . '/'. $brochure_name;
                Storage::disk('store_brochures')->put($brochure_name, $contents_bro);
                $input['brochure'] = $brochure_path;*/
            }
            else {
                if(isset($request->is_brochure) && ($request->is_brochure == 0)){
                    Storage::delete('public/' . $store->brochure);
                    $input['brochure'] = null;
                }
            }
            $store->fill($input)->save();

            $store_single = Store::find($store->id);
            $store_single->pins()->sync($request->pins);

            $categories = Store::find($store->id);
            $categories->categories()->sync($request->category);

            if ($request->ajax()) {
                return response()->json(['success' => 'New store has been added successfully']);
            } else {
                return redirect()->route('admin.stores.index')->with('success', 'New store has been added successfully');
            }
        }
    }

    public function destroy($id)
    {
        $store = Store::find($id);
        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Store')->first();
        $all_slug->delete();

        if(!empty($store->image) && $store->image != null){
            Storage::delete('public/' . $store->image);
        }
        if(!empty($store->brochure) && $store->brochure != null){
            Storage::delete('public/' . $store->brochure);
        }
        $store->delete();

        return response()->json(['success' => 'Store has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Store::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public function sidebar_categories($store_id='')
    {
        $store_categories = [];
        if($store_id !='') {
            $store = Store::find($store_id);
            $store_category_array = $store->categories;
            foreach ($store_category_array as $store_category) {
                $store_categories[] = $store_category->id;
            }
        }else {
            $store_categories = [];
        }

        $categories = Category::has('parents','0')->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')->get();
        $content = '<ul class="list-group">';
        foreach ($categories as $category) {
            $content .= $this->show_category_lists($category,$store_categories);
        }
        $content .= '</ul>';

        return ['content'=>$content];
    }
    public  function show_category_lists($category,$store_categories) {
        /*$child_categories = Category::where('parent',$category->id)->get();*/
        $child_categories = $category->childs;
        $child_content = '';
        if($child_categories) {
            $child_content .= '<div style="padding-left:20px;margin-top:10px;">
                                    <ul class="list-group">';
            foreach ($child_categories as $child_category) {
                $isChecked = in_array($child_category->id, $store_categories) ? 'checked' : '';
                $child_content .= '<li class="list-group-item">
                                            <input type="checkbox" name="category[]" id="child_category_' . $child_category->id . '" value="' . $child_category->id . '" ' . $isChecked . '   />
                                            <label for="child_category_' . $child_category->id . '">
                                                ' . $child_category->name . '
                                            </label>
                                        </li>';
            }
            $child_content .= '</ul>
                                </div>';
        }

        $content = '<li class="list-group-item">
                            <div class="checkbox">';
        /*if($category->id == Utility::CATEGORY_ID_OFFER) {
            $isChecked2 = in_array($category->id, $store_categories) ? 'checked' : '';
            $content .= '<input type="checkbox" name="category[]" id="category_' . $category->id . '" value="' . $category->id . '" ' . $isChecked2 . ' />';
        }*/
        $content .= '<label for="category_' . $category->id . '">
                                    <strong>' . $category->name . '</strong>
                                </label>' . $child_content .
            '</div>
                        </li>';
        return $content;
    }

}
