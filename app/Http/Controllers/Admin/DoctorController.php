<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\Services\Slug;
use App\Models\Doctor;
use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use App\Http\Utilities\Utility;

class DoctorController extends Controller
{
    public  function index() {
        return view('admin.pages.doctors.index');
    }
    public  function data() {
        $doctors = Doctor::select()->latest();
        return DataTables::of($doctors)
            ->rawColumns(['name','designation','description','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->editColumn('designation', function ($modal) {
                return '<p>' . $modal->designation . '</p>';
            })
            ->editColumn('description', function ($modal) {

                return '<p>' . $modal->description . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.doctors.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.doctors.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.doctors.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('doctor_{{$id}}')
            ->make(true);
    }
    public function create() {
        $treatments = Treatment::pluck('name','id');
        return view('admin.pages.doctors.add',['treatments'=> $treatments]);
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
                return redirect()->route('admin.doctors.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $doctor = new Doctor;
            $input = $request->only(['name','designation','avail_time','experience','description','site_title','site_keywords','site_description']);
            $input['is_active'] = 1;
            $doctor->fill($input)->save();

            $slug = new Slug();
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $doctor->id,
                'causer_type' => 'App\Models\Doctor',
                'slug' => $slug_data,
            ]);
            $all_slug->save();

            $imgUploadSizeOriginal = Utility::IMAGE_DOCTOR_ORIGINAL;
            $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
            $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

            $imgUploadSizeThumb = Utility::IMAGE_DOCTOR_THUMB;
            $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
            $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

            if($request->hasFile('image')) {
                $image = $request->file('image');

                $image_name_original = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::cleanString($image->getClientOriginalName());
                $image_name_thumb = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::cleanString($image->getClientOriginalName());

                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Doctor::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image']['tmp_name'];
                $contents = file_get_contents($realPath);

                $contents_original = \Image::make($contents);
                $contents_thumb = \Image::make($contents);

                $image_path_original = Doctor::FILE_DIRECTORY . '/'. $image_name_original;
                $image_path_thumb = Doctor::FILE_DIRECTORY . '/'. $image_name_thumb;


                $contents_original->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
                    $constraint->aspectRatio();
                });
                Storage::disk('doctors')->put($image_name_original, $contents_original->encode());

                $contents_thumb->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
                    $constraint->aspectRatio();
                });
                Storage::disk('doctors')->put($image_name_thumb, $contents_thumb->encode());

                $image_paths = ['thumb' => $image_path_thumb, 'original' => $image_path_original];
                $doctor->image = $image_paths;

            }

            $imgsUploadSizeOriginal = Utility::IMAGES_DOCTOR_ORIGINAL;
            $imgsUploadSizeOriginalW = Utility::getImageDimension($imgsUploadSizeOriginal)['width'];
            $imgsUploadSizeOriginalH = Utility::getImageDimension($imgsUploadSizeOriginal)['height'];

            $imgsUploadSizeThumb = Utility::IMAGES_DOCTOR_THUMB;
            $imgsUploadSizeThumbW = Utility::getImageDimension($imgsUploadSizeThumb)['width'];
            $imgsUploadSizeThumbH = Utility::getImageDimension($imgsUploadSizeThumb)['height'];

            if($request->hasFile('images')) {
                $images = $request->file('images');

                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Doctor::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $images_path = [];
                foreach($images as $index => $image) {
                    $image_name_o = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgsUploadSizeThumb) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $image_name_o_original = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgsUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $realPath_o = $_FILES['images']['tmp_name'][$index];
                    $contents_o = file_get_contents($realPath_o);
                    $contents_o = \Image::make($contents_o);
                    $contents_o_original = \Image::make($contents_o);
                    $image_path_o = Doctor::FILE_DIRECTORY . '/' . $image_name_o;
                    $image_path_o_original = Doctor::FILE_DIRECTORY . '/' . $image_name_o_original;

                    $contents_o->fit($imgsUploadSizeOriginalW, $imgsUploadSizeOriginalH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('doctors')->put($image_name_o_original, $contents_o_original->encode());

                    $contents_o->fit($imgsUploadSizeThumbW, $imgsUploadSizeThumbH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('doctors')->put($image_name_o, $contents_o->encode());

                    $images_path[] = ['thumb'=>$image_path_o, 'original'=>$image_path_o_original];
                }
                $doctor->images = $images_path;
            }

            /*if($request->hasFile('image')) {
                $image = $request->file('image');
                $image_name = $doctor->id . '_' . str_replace(' ','_',$image->getClientOriginalName());
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Doctor::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image']['tmp_name'];
                $contents = file_get_contents($realPath);
                $contents = \Image::make($contents);
                $image_path = Doctor::FILE_DIRECTORY . '/'. $image_name;

                Storage::disk('doctors')->put($image_name, $contents->encode());
                $doctor->image = $image_path;
            }*/

            $doctor->save();

            if($request->has('treatments')) {
                $doctor_single = Doctor::find($doctor->id);
                $doctor_single->treatments()->attach($request->treatments);
            }

            if ($request->ajax()) {
                return response()->json(['success' => 'New doctor has been added successfully']);
            } else {
                return redirect()->route('admin.doctors.index')->with('success', 'New doctor has been added successfully');
            }
        }
    }
    public function edit($id) {

        $treatments = Treatment::pluck('name','id');
        $doctor = Doctor::findOrFail($id);

        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Doctor')->first();
        $slug = $all_slug->slug;

        $treatment_id_array = [];
        foreach($doctor->treatments as $item) {
            $treatment_id_array[] = $item['id'];
        }

        return view('admin.pages.doctors.add',['doctor'=>$doctor,'treatments'=> $treatments, 'treatment_id_array'=> $treatment_id_array, 'slug'=>$slug]);
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
                return redirect()->route('admin.doctors.edit',$id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $doctor = Doctor::find($id);
            $input = $request->only(['name','designation','avail_time','experience','description','site_title','site_keywords','site_description']);


            $slug = new Slug();
            $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Doctor')->first();
            if($all_slug) {
                if ($doctor->name != $request->name) {
                    $slug_data = $slug->createSlug($request->name, $id);
                    $all_slug->fill(['slug' => $slug_data])->save();
                }
            }else {
                $slug_data = $slug->createSlug($request->name);

                $all_slug = new AllSlug();
                $all_slug->fill([
                    'causer_id' => $doctor->id,
                    'causer_type' => 'App\Models\Doctor',
                    'slug' => $slug_data,
                ]);
                $all_slug->save();
            }

            $doctor->fill($input)->save();

            $imgUploadSizeOriginal = Utility::IMAGE_DOCTOR_ORIGINAL;
            $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
            $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

            $imgUploadSizeThumb = Utility::IMAGE_DOCTOR_THUMB;
            $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
            $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

            if($request->hasFile('image')) {
                $this->destroy_image($doctor->image['thumb']);
                $this->destroy_image($doctor->image['original']);

                $image = $request->file('image');

                $image_name_original = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::cleanString($image->getClientOriginalName());
                $image_name_thumb = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::cleanString($image->getClientOriginalName());

                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Doctor::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image']['tmp_name'];
                $contents = file_get_contents($realPath);

                $contents_original = \Image::make($contents);
                $contents_thumb = \Image::make($contents);

                $image_path_original = Doctor::FILE_DIRECTORY . '/'. $image_name_original;
                $image_path_thumb = Doctor::FILE_DIRECTORY . '/'. $image_name_thumb;

                $contents_original->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
                    $constraint->aspectRatio();
                });
                Storage::disk('doctors')->put($image_name_original, $contents_original->encode());

                $contents_thumb->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
                    $constraint->aspectRatio();
                });
                Storage::disk('doctors')->put($image_name_thumb, $contents_thumb->encode());

                $image_paths = ['thumb' => $image_path_thumb, 'original' => $image_path_original];
                $input['image'] = $image_paths;
            }
            else {
                if(isset($request->is_image) && ($request->is_image == 0)){
                    $this->destroy_image($doctor->image['thumb']);
                    $this->destroy_image($doctor->image['original']);

                    $input['image'] = null;
                }
            }

            $imgsUploadSizeOriginal = Utility::IMAGES_DOCTOR_ORIGINAL;
            $imgsUploadSizeOriginalW = Utility::getImageDimension($imgsUploadSizeOriginal)['width'];
            $imgsUploadSizeOriginalH = Utility::getImageDimension($imgsUploadSizeOriginal)['height'];

            $imgsUploadSizeThumb = Utility::IMAGES_DOCTOR_THUMB;
            $imgsUploadSizeThumbW = Utility::getImageDimension($imgsUploadSizeThumb)['width'];
            $imgsUploadSizeThumbH = Utility::getImageDimension($imgsUploadSizeThumb)['height'];

            if($request->hasFile('images')) {
                if(!empty($doctor->images)) {
                    foreach ($doctor->images as $doctor_image) {
                        $this->destroy_image($doctor_image['thumb']);
                        $this->destroy_image($doctor_image['original']);
                    }
                }
                $images = $request->file('images');
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Doctor::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $images_path = [];
                foreach($images as $index => $image) {
                    $image_name_o = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgsUploadSizeThumb) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $image_name_o_original = Utility::addUnderScore($doctor->id) . Utility::addUnderScore($imgsUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $realPath_o = $_FILES['images']['tmp_name'][$index];
                    $contents_o = file_get_contents($realPath_o);
                    $contents_o = \Image::make($contents_o);
                    $contents_o_original = \Image::make($contents_o);
                    $image_path_o = Doctor::FILE_DIRECTORY . '/' . $image_name_o;
                    $image_path_o_original = Doctor::FILE_DIRECTORY . '/' . $image_name_o_original;

                    $contents_o->fit($imgsUploadSizeOriginalW, $imgsUploadSizeOriginalH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('doctors')->put($image_name_o_original, $contents_o_original->encode());

                    $contents_o->fit($imgsUploadSizeThumbW, $imgsUploadSizeThumbH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('doctors')->put($image_name_o, $contents_o->encode());
                    $images_path[] = ['thumb'=>$image_path_o, 'original'=>$image_path_o_original];
                }
                $input['images'] = $images_path;
            }
            else {
                if(isset($request->is_images) && ($request->is_images == 0)){
                    if(!empty($doctor->images)) {
                        foreach ($doctor->images as $doctor_image) {
                            $this->destroy_image($doctor_image['thumb']);
                            $this->destroy_image($doctor_image['original']);
                        }
                    }
                    $input['images'] = null;
                }
            }

            /*if($request->hasFile('image')) {
                if(!empty($doctor->image) && $doctor->image != null){
                    Storage::delete('public/' . $doctor->image);
                }
                $image = $request->file('image');
                $image_name = $doctor->id . '_' . str_replace(' ','_',$image->getClientOriginalName());
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Doctor::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $realPath = $_FILES['image']['tmp_name'];
                $contents = file_get_contents($realPath);
                $contents = \Image::make($contents);
                $image_path = Doctor::FILE_DIRECTORY . '/'. $image_name;

                Storage::disk('doctors')->put($image_name, $contents->encode());
                $input['image'] = $image_path;
            }
            else {
                if(isset($request->is_image) && ($request->is_image == 0)){
                    Storage::delete('public/' . $doctor->image);
                    $input['image'] = null;
                }
            }*/

            $doctor->fill($input)->save();

            $doctor_single = Doctor::find($doctor->id);
            $doctor_single->treatments()->sync($request->treatments);

            if ($request->ajax()) {
                return response()->json(['success' => 'New doctor has been added successfully']);
            } else {
                return redirect()->route('admin.doctors.index')->with('success', 'New doctor has been added successfully');
            }
        }
    }

    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if(!empty($doctor->image) && $doctor->image != null){
            Storage::delete('public/' . $doctor->image);
        }
        $doctor->delete();

        return response()->json(['success' => 'Doctor has been deleted successfully']);
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
        $model = Doctor::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
