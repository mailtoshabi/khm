<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\City;
use App\Models\ClinicType;
use App\Models\Doctor;
use App\Models\Services\Slug;
use App\Models\Clinic;
use App\Models\Treatment;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use Validator;
use File;
use Storage;
use Image;
use App\Http\Utilities\Utility;

class ClinicController extends Controller
{
    public  function index() {
        return view('admin.pages.clinics.index');
    }
    public  function data() {
        /*$clinics = Clinic::select()->latest();*/
        $clinics = User::whereHas('roles' , function($q){
            $q->where('id', Utility::CLINIC_ROLE_ID);
        })->select()->latest();
        return DataTables::of($clinics)
            ->rawColumns(['name','email','type','city','action'])
            ->editColumn('name', function ($modal) {
                $all_slug = AllSlug::where('causer_id',$modal->clinic->id)->where('causer_type', 'App\Models\Clinic')->first();
                $slug = $all_slug->slug;
                $data = '<p>' . $modal->name . '</p>';
                $data .= '<a href="'. config('app.website_url') . '/' . $slug . '" target="_blank">'. config('app.website_url') . '/' . $slug . '</a>';
                return $data;
            })
            ->editColumn('email', function ($modal) {
                return '<p>' . $modal->email . '</p>';
            })
            ->addColumn('type', function ($modal) {
                return '<p>' . Utility::clinic_types()[$modal->clinic->type] . '</p>';
            })
            ->editColumn('city', function ($modal) {
                return '<p>' . $modal->clinic->get_city() . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.clinics.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.clinics.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.clinics.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('clinic_{{$id}}')
            ->make(true);
    }
    public function create() {
        $doctors = Doctor::pluck('name','id');
        $treatments = Treatment::pluck('name','id');
        $types=ClinicType::pluck('name','id');
        $districts = DB::table('districts')->where('state_id',Utility::STATE_ID_KERALA)->pluck('name','id');
        $cities = City::pluck('name','id');
        return view('admin.pages.clinics.add',['doctors'=> $doctors,'treatments'=> $treatments,'types'=> $types,'districts'=> $districts,'cities'=> $cities]);
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required',
            'email' => 'required',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.clinics.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $user = new User;
            $user_input = $request->only(['name','email','username']);
            $user_input['password'] = bcrypt($request->password);
            $user_input['is_active'] = 1;
            $user->fill($user_input)->save();

            $user_details = new UserDetail;
            $user_details_input = [];
            $user_details_input['user_id'] = $user->id;
            $user_details->fill($user_details_input)->save();



            $clinic = new Clinic;
            $input = $request->only(['type','description','footer_description','location','city','district','pin','contact_email','phone','location_link','oh_mon','oh_tue','oh_wed','oh_thu','oh_fri','oh_sat','oh_sun','site_title','site_keywords','site_description']);
            $input['user_id'] = $user->id;
            $input['is_oh'] = isset($request->is_oh) ? 1 : 0;
            $input['is_active'] = 1;
            $clinic->fill($input)->save();

            $slug = new Slug();
            $slug_data = $slug->createSlug($request->slug);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $clinic->id,
                'causer_type' => 'App\Models\Clinic',
                'slug' => $slug_data,
            ]);
            $all_slug->save();

            DB::table('role_user')->insert(
                ['user_id' => $user->id, 'role_id' => Utility::CLINIC_ROLE_ID]
            );

            $imgUploadSizeOriginal = Utility::IMAGE_CLINIC_ORIGINAL;
            $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
            $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

            $imgUploadSizeThumb = Utility::IMAGE_CLINIC_THUMB;
            $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
            $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

            if($request->hasFile('images')) {
                $images = $request->file('images');

                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Clinic::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $images_path = [];
                foreach($images as $index => $image) {
                    $image_name_o = Utility::addUnderScore($clinic->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $image_name_o_original = Utility::addUnderScore($clinic->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $realPath_o = $_FILES['images']['tmp_name'][$index];
                    $contents_o = file_get_contents($realPath_o);
                    $contents_o = \Image::make($contents_o);
                    $contents_o_original = \Image::make($contents_o);
                    $image_path_o = Clinic::FILE_DIRECTORY . '/' . $image_name_o;
                    $image_path_o_original = Clinic::FILE_DIRECTORY . '/' . $image_name_o_original;

                    $contents_o->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('clinics')->put($image_name_o_original, $contents_o_original->encode());

                    $contents_o->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('clinics')->put($image_name_o, $contents_o->encode());

                    $images_path[] = ['thumb'=>$image_path_o, 'original'=>$image_path_o_original];
                }
                $clinic->images = $images_path;
            }

            $clinic->save();

            if($request->has('doctors')) {
                $clinic_single = Clinic::find($clinic->id);
                $clinic_single->doctors()->attach($request->doctors);
            }

            if($request->has('treatments')) {
                $clinic_single = Clinic::find($clinic->id);
                $clinic_single->treatments()->attach($request->treatments);
            }

            if ($request->ajax()) {
                return response()->json(['success' => 'New clinic has been added successfully']);
            } else {
                return redirect()->route('admin.clinics.index')->with('success', 'New clinic has been added successfully');
            }
        }
    }
    public function edit($id) {

        $user = User::findOrFail($id);

        $doctors = Doctor::pluck('name','id');
        $treatments = Treatment::pluck('name','id');

        /*$clinic = Clinic::findOrFail($id);*/
        $clinic = Clinic::where('user_id', $user->id)->first();

        $types=ClinicType::pluck('name','id');
        $districts = DB::table('districts')->where('state_id',Utility::STATE_ID_KERALA)->pluck('name','id');
        $cities = City::pluck('name','id');

        $all_slug = AllSlug::where('causer_id',$user->clinic->id)->where('causer_type', 'App\Models\Clinic')->first();
        $slug = $all_slug->slug;

        $doctor_id_array = [];
        foreach($clinic->doctors as $item) {
            $doctor_id_array[] = $item['id'];
        }

        $treatment_id_array = [];
        foreach($clinic->treatments as $item) {
            $treatment_id_array[] = $item['id'];
        }
        return view('admin.pages.clinics.add',['user'=>$user,'clinic'=>$clinic,'doctors'=> $doctors,'treatments'=> $treatments, 'doctor_id_array'=> $doctor_id_array, 'treatment_id_array'=> $treatment_id_array, 'slug'=>$slug, 'types'=>$types, 'districts'=>$districts, 'cities'=>$cities]);
    }
    public function update(Request $request, $id) {
        $rules = [
            'name' => 'required',
            'email' => 'required',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.clinics.edit',$id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        else {
            $user = User::findOrFail($id);

            $clinic = Clinic::where('user_id', $user->id)->first();

            $slug = new Slug();
            $all_slug = AllSlug::where('causer_id',$clinic->id)->where('causer_type', 'App\Models\Clinic')->first();
            if($all_slug) {
                if ($all_slug->slug != str_slug($request->slug)) {
                    $slug_data = $slug->createSlug($request->slug, $id);
                    $all_slug->fill(['slug' => $slug_data])->save();
                }
            }else {
                $slug_data = $slug->createSlug($request->slug);

                $all_slug = new AllSlug();
                $all_slug->fill([
                    'causer_id' => $clinic->id,
                    'causer_type' => 'App\Models\Clinic',
                    'slug' => $slug_data,
                ]);
                $all_slug->save();
            }


            $user_input = $request->only(['name','email','username']);
            if(!empty($request->password)) {
                $user_input['password'] = bcrypt($request->password);
            }
            $user->fill($user_input)->save();

            $input = $request->only(['type','description','footer_description','location','city','district','pin','contact_email','phone','location_link','oh_mon','oh_tue','oh_wed','oh_thu','oh_fri','oh_sat','oh_sun','site_title','site_keywords','site_description']);
            $input['is_oh'] = isset($request->is_oh) ? 1 : 0;

            $clinic->fill($input)->save();

            $imgUploadSizeOriginal = Utility::IMAGE_CLINIC_ORIGINAL;
            $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
            $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

            $imgUploadSizeThumb = Utility::IMAGE_CLINIC_THUMB;
            $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
            $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

            if($request->hasFile('images')) {
                if(!empty($clinic->images)) {
                    foreach ($clinic->images as $clinic_image) {
                        $this->destroy_image($clinic_image['thumb']);
                        $this->destroy_image($clinic_image['original']);
                    }
                }
                $images = $request->file('images');
                $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Clinic::FILE_DIRECTORY);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }
                $images_path = [];
                foreach($images as $index => $image) {
                    $image_name_o = Utility::addUnderScore($clinic->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $image_name_o_original = Utility::addUnderScore($clinic->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::addUnderScore($index+1) . Utility::cleanString($image->getClientOriginalName());
                    $realPath_o = $_FILES['images']['tmp_name'][$index];
                    $contents_o = file_get_contents($realPath_o);
                    $contents_o = \Image::make($contents_o);
                    $contents_o_original = \Image::make($contents_o);
                    $image_path_o = Clinic::FILE_DIRECTORY . '/' . $image_name_o;
                    $image_path_o_original = Clinic::FILE_DIRECTORY . '/' . $image_name_o_original;

                    $contents_o->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('clinics')->put($image_name_o_original, $contents_o_original->encode());

                    $contents_o->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::disk('clinics')->put($image_name_o, $contents_o->encode());
                    $images_path[] = ['thumb'=>$image_path_o, 'original'=>$image_path_o_original];
                }
                $input['images'] = $images_path;
            }
            else {
                if(isset($request->is_images) && ($request->is_images == 0)){
                    if(!empty($clinic->images)) {
                        foreach ($clinic->images as $clinic_image) {
                            $this->destroy_image($clinic_image['thumb']);
                            $this->destroy_image($clinic_image['original']);
                        }
                    }
                    $input['images'] = null;
                }
            }

            $clinic->fill($input)->save();

            $clinic_single = Clinic::find($clinic->id);
            $clinic_single->doctors()->sync($request->doctors);

            $clinic_single = Clinic::find($clinic->id);
            $clinic_single->treatments()->sync($request->treatments);

            if ($request->ajax()) {
                return response()->json(['success' => 'New clinic has been added successfully']);
            } else {
                return redirect()->route('admin.clinics.index')->with('success', 'New clinic has been added successfully');
            }
        }
    }

    public function destroy($id)
    {
        $all_slug = AllSlug::where('causer_id',$id)->where('causer_type', 'App\Models\Clinic')->first();
        $all_slug->delete();

        $user = User::findOrFail($id);
        $clinic = Clinic::where('user_id', $id)->first();

        if(!empty($clinic->images) && $clinic->images != null){
            foreach ($clinic->images as $clinic_image) {
                $this->destroy_image($clinic_image['thumb']);
                $this->destroy_image($clinic_image['original']);
            }
        }

        $user->delete();

        return response()->json(['success' => 'Clinic has been deleted successfully']);
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

        $model = Clinic::where('user_id', $user->id)->first();
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public function destroy_image($image)
    {
        if(!empty($image) && $image != null){
            Storage::delete('public/' . $image);
        }
        return 1;
    }

    public  function doctor_index() {
        return view('admin.pages.clinics.doctors');
    }

    public  function doctor_data() {
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
                $user = User::find(Auth::id());
                $clinic_id = $user->clinic->id;
                $clinic_doctor = DB::table('clinic_doctor')->where(['clinic_id' => $clinic_id, 'doctor_id' => $modal->id])->first();
                $avail_time = $clinic_doctor ? $clinic_doctor->avail_time : '';
                return '<p>' . $avail_time . '</p>';
            })
            /*->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.doctors.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.doctors.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.doctors.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })*/
            ->addColumn('action', function ($modal) {
                $user = User::find(Auth::id());
                $clinic_id = $user->clinic->id;
                $is_added = DB::table('clinic_doctor')->where(['clinic_id' => $clinic_id, 'doctor_id' => $modal->id])->first();
                $publishIcon = $is_added ? 'fa fa-pencil' : 'fa fa-plus';
                $publishTitle = $is_added ? 'Edit Doctor' : 'Add Doctor';
                $data = '<a  href="'. route('admin.clinics.add_doctor',[$modal->id]) . '" title="' . $publishTitle . '" > <i class="' . $publishIcon . ' text-primary"></i></a>';
                if($is_added) {
                    $data .= '&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.clinics.remove_doctor',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Do you really need to remove the doctor from your list." data-type="GET" title="Remove Doctor"> <i class="fa fa-remove text-primary"></i></a>';
                }
                return $data;
            })
            ->setRowId('doctor_{{$id}}')
            ->make(true);
    }

    public function add_doctor($id) {
        $doctor = Doctor::findOrFail($id);
        $user = User::find(Auth::id());
        $clinic_id = $user->clinic->id;
        $clinic_doctor = DB::table('clinic_doctor')->where(['clinic_id' => $clinic_id, 'doctor_id' => $id])->first();

        return view('admin.pages.clinics.add-doctor',['doctor'=>$doctor, 'clinic_id'=>$clinic_id, 'clinic_doctor'=>$clinic_doctor]);
    }


    public function add_doctor_update(Request $request,$id) {

        $clinic_doctor = DB::table('clinic_doctor')
            ->where(['clinic_id' => $request->clinic_id, 'doctor_id' => $request->doctor_id])
            ->get();
        if(!empty(json_decode($clinic_doctor))) {
            DB::table('clinic_doctor')
                ->where(['clinic_id' => $request->clinic_id, 'doctor_id' => $request->doctor_id])
                ->update(
                    ['avail_time' => $request->avail_time]
                );
        }else {
            DB::table('clinic_doctor')
                ->insert(
                    ['clinic_id' => $request->clinic_id, 'doctor_id' => $request->doctor_id, 'avail_time' => $request->avail_time]
                );
        }

        /*$clinic_doctor = DB::table('clinic_doctor')
            ->insert(
                ['clinic_id' => $request->clinic_id, 'doctor_id' => $request->doctor_id, 'avail_time' => $request->avail_time]
            );*/
        $doctor = Doctor::findOrFail($id);
        $user = User::find(Auth::id());
        $clinic_id = $user->clinic->id;
        return redirect()->route('admin.clinics.doctor')->with(['success' => 'Doctor has been added from your list successfully','doctor'=>$doctor, 'clinic_id'=>$clinic_id, 'clinic_doctor'=>$clinic_doctor]);
    }

    public function remove_doctor($id) {
        $user = User::find(Auth::id());
        $clinic_id = $user->clinic->id;

        $clinic_doctor = DB::table('clinic_doctor')
            ->where(['clinic_id' => $clinic_id, 'doctor_id' => $id])
            ->delete();
        return response()->json(['success' => 'Doctor has been Removed from your list successfully']);
    }

}
