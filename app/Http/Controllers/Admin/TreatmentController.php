<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\Doctor;
use App\Models\Services\Slug;
use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use App\Http\Utilities\Utility;

class TreatmentController extends Controller
{
    public  function index() {
        return view('admin.pages.treatments.index');
    }
    public  function data() {
        $treatments = Treatment::select()->latest();
        return DataTables::of($treatments)
            ->rawColumns(['name','description','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->editColumn('description', function ($modal) {
                return '<p>' . $modal->description . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.treatments.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.treatments.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.treatments.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('treatment_{{$id}}')
            ->make(true);
    }
    public function create() {
        $doctors = Doctor::pluck('name','id');
        return view('admin.pages.treatments.add',['doctors'=> $doctors]);
    }

    public function store(Request $request) {
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
                return redirect()->route('admin.treatments.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $treatment = new Treatment;
            $input = $request->only(['name','description','site_title','site_keywords','site_description']);
            $input['is_active'] = 1;
            $treatment->fill($input)->save();

            $slug = new Slug();
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $treatment->id,
                'causer_type' => 'App\Models\Treatment',
                'slug' => $slug_data,
            ]);
            $all_slug->save();

            $imgUploadSizeOriginal = Utility::IMAGE_TREATMENT_ORIGINAL;
            $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
            $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

            $imgUploadSizeThumb = Utility::IMAGE_TREATMENT_THUMB;
            $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
            $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

            if($request->hasFile('images')) {
                $images = $request->file('images');

                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Treatment::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $images_path = [];
                foreach($images as $index => $image) {
                    $image_name_o = Utility::addUnderScore($treatment->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $image_name_o_original = Utility::addUnderScore($treatment->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $realPath_o = $_FILES['images']['tmp_name'][$index];
                    $contents_o = file_get_contents($realPath_o);
                    $contents_o = \Image::make($contents_o);
                    $contents_o_original = \Image::make($contents_o);
                    $image_path_o = Treatment::FILE_DIRECTORY . '/' . $image_name_o;
                    $image_path_o_original = Treatment::FILE_DIRECTORY . '/' . $image_name_o_original;

                    $contents_o->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('treatments')->put($image_name_o_original, $contents_o_original->encode());

                    $contents_o->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('treatments')->put($image_name_o, $contents_o->encode());

                    $images_path[] = ['thumb'=>$image_path_o, 'original'=>$image_path_o_original];
                }
                $treatment->images = $images_path;
            }

            $treatment->save();

            if($request->has('doctors')) {
                $treatment_single = Treatment::find($treatment->id);
                $treatment_single->doctors()->attach($request->doctors);
            }

            if ($request->ajax()) {
                return response()->json(['success' => 'New treatment has been added successfully']);
            } else {
                return redirect()->route('admin.treatments.index')->with('success', 'New treatment has been added successfully');
            }
        }
    }
    public function edit($id) {

        $doctors = Doctor::pluck('name','id');
        $treatment = Treatment::findOrFail($id);

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Treatment')->first();
        $slug = $all_slug->slug;

        $doctor_id_array = [];
        foreach($treatment->doctors as $item) {
            $doctor_id_array[] = $item['id'];
        }

        return view('admin.pages.treatments.add',['treatment'=>$treatment,'doctors'=> $doctors, 'doctor_id_array'=> $doctor_id_array, 'slug'=>$slug]);
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
                return redirect()->route('admin.treatments.edit',$id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $treatment = Treatment::find($id);
            $input = $request->only(['name','description','site_title','site_keywords','site_description']);

            $slug = new Slug();
            $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Treatment')->first();
            if($all_slug) {
                if ($treatment->name != $request->name) {
                    $slug_data = $slug->createSlug($request->name, $id);
                    $all_slug->fill(['slug' => $slug_data])->save();
                }
            }else {
                $slug_data = $slug->createSlug($request->name);

                $all_slug = new AllSlug();
                $all_slug->fill([
                    'causer_id' => $treatment->id,
                    'causer_type' => 'App\Models\Treatment',
                    'slug' => $slug_data,
                ]);
                $all_slug->save();
            }
            $treatment->fill($input)->save();

            $imgUploadSizeOriginal = Utility::IMAGE_TREATMENT_ORIGINAL;
            $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
            $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

            $imgUploadSizeThumb = Utility::IMAGE_TREATMENT_THUMB;
            $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
            $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

            if($request->hasFile('images')) {
                if(!empty($treatment->images)) {
                    foreach ($treatment->images as $treatment_image) {
                        $this->destroy_image($treatment_image['thumb']);
                        $this->destroy_image($treatment_image['original']);
                    }
                }
                $images = $request->file('images');
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Treatment::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $images_path = [];
                foreach($images as $index => $image) {
                    $image_name_o = Utility::addUnderScore($treatment->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $image_name_o_original = Utility::addUnderScore($treatment->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $realPath_o = $_FILES['images']['tmp_name'][$index];
                    $contents_o = file_get_contents($realPath_o);
                    $contents_o = \Image::make($contents_o);
                    $contents_o_original = \Image::make($contents_o);
                    $image_path_o = Treatment::FILE_DIRECTORY . '/' . $image_name_o;
                    $image_path_o_original = Treatment::FILE_DIRECTORY . '/' . $image_name_o_original;

                    $contents_o->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('treatments')->put($image_name_o_original, $contents_o_original->encode());

                    $contents_o->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('treatments')->put($image_name_o, $contents_o->encode());
                    $images_path[] = ['thumb'=>$image_path_o, 'original'=>$image_path_o_original];
                }
                $input['images'] = $images_path;
            }
            else {
                if(isset($request->is_images) && ($request->is_images == 0)){
                    if(!empty($treatment->images)) {
                        foreach ($treatment->images as $treatment_image) {
                            $this->destroy_image($treatment_image['thumb']);
                            $this->destroy_image($treatment_image['original']);
                        }
                    }
                    $input['images'] = null;
                }
            }
















            $treatment->fill($input)->save();

            $treatment_single = Treatment::find($treatment->id);
            $treatment_single->doctors()->sync($request->doctors);

            if ($request->ajax()) {
                return response()->json(['success' => 'New treatment has been added successfully']);
            } else {
                return redirect()->route('admin.treatments.index')->with('success', 'New treatment has been added successfully');
            }
        }
    }

    public function destroy($id)
    {
        $treatment = Treatment::find($id);

        if(!empty($treatment->image) && $treatment->image != null){
            Storage::delete('public/' . $treatment->image);
        }
        $treatment->delete();

        return response()->json(['success' => 'Treatment has been deleted successfully']);
    }

    public function destroy_image($image)
    {
        if(!empty($image) && $image != null){
            Storage::delete('public/' . $image);
        }
        return 1;
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Treatment::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

}
