<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllSlug;
use App\Models\Affiliate;
use App\Models\Services\Slug;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use File;
use Intervention\Image\Facades\Image as ResizeImage;
use App\Http\Utilities\Utility;
use Illuminate\Support\Facades\Storage;

class AffiliateController extends Controller
{
    public  function index() {
        return view('admin.pages.affiliates.index');
    }
    public  function data() {
        $affiliates = User::whereHas('roles' , function($q){
            $q->where('id', Utility::AFFILIATE_ROLE_ID);
        })->select()->latest();
        return DataTables::of($affiliates)
            ->rawColumns(['name','email','action'])
            ->editColumn('name', function ($modal) {
                $all_slug = AllSlug::where('causer_id',$modal->affiliate->id)->where('causer_type', 'App\Models\Affiliate')->first();
                $slug = $all_slug->slug;
                $data = '<p>' . $modal->name . '</p>';
                $data .= '<a href="'. config('app.website_url') . '/' . $slug . '" target="_blank">'. config('app.website_url') . '/' . $slug . '</a>';
                return $data;
            })
            ->editColumn('email', function ($modal) {
                return '<p>' . $modal->email . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a  href="'. route('admin.affiliates.edit',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.affiliates.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.affiliates.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('affiliate_{{$id}}')
            ->make(true);
    }
    public function create() {
        return view('admin.pages.affiliates.add');
    }

    public function store(Request $request) {

        $validated = request()->validate([
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
        ]);

        // $rules = [
        //     'name' => 'required',
        //     'email' => 'required',
        // ];
        // $messages = [
        //     'required' => 'The :attribute field is required.',
        // ];
        // $validator = Validator::make($request->all(), $rules, $messages);
        // if ($validator->fails()) {
        //     if($request->ajax()) {
        //         return response()->json($validator->errors(), 422);
        //     } else {
        //         return redirect()->route('admin.affiliates.create')
        //             ->withErrors($validator)
        //             ->withInput();
        //     }
        // }
        // else {

        // }
        $user = new User;
        $user_input = $request->only(['name','email','username']);
        $user_input['password'] = bcrypt($request->password);
        $user_input['is_active'] = 1;
        $user->fill($user_input)->save();

        $user_details = new UserDetail;
        $user_details_input = [];
        $user_details_input['user_id'] = $user->id;
        $user_details->fill($user_details_input)->save();

        $affiliate = new Affiliate;
        $input = $request->only(['description','location','city','district','pin','contact_email','contact_phone','contact_whatsapp','footer_description','site_title','site_keywords','site_description']);
        $input['user_id'] = $user->id;
        $affiliate->fill($input)->save();

        $slug = new Slug();
        $slug_data = $slug->createSlug($request->name);

        $all_slug = new AllSlug();
        $all_slug->fill([
            'causer_id' => $affiliate->id,
            'causer_type' => 'App\Models\Affiliate',
            'slug' => $slug_data,
        ]);
        $all_slug->save();

        DB::table('role_user')->insert(
            ['user_id' => $user->id, 'role_id' => Utility::AFFILIATE_ROLE_ID]
        );

        $imgUploadSizeOriginal = Utility::IMAGE_AFFILIATE_ORIGINAL;
        $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
        $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

        $imgUploadSizeThumb = Utility::IMAGE_AFFILIATE_THUMB;
        $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
        $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

        // if($request->hasFile('image')) {
        //     $image = $request->file('image');

        //     $image_name_original = Utility::addUnderScore($affiliate->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::cleanString($image->getClientOriginalName());
        //     $image_name_thumb = Utility::addUnderScore($affiliate->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::cleanString($image->getClientOriginalName());

        //     $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Affiliate::FILE_DIRECTORY);
        //     if (!File::exists($destinationPath)) {
        //         File::makeDirectory($destinationPath, $mode = 0777, true, true);
        //     }
        //     $realPath = $_FILES['image']['tmp_name'];
        //     $contents = file_get_contents($realPath);

        //     $contents_original = \Image::make($contents);
        //     $contents_thumb = \Image::make($contents);

        //     $image_path_original = Affiliate::FILE_DIRECTORY . '/'. $image_name_original;
        //     $image_path_thumb = Affiliate::FILE_DIRECTORY . '/'. $image_name_thumb;


        //     $contents_original->fit($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        //     Storage::disk('affiliates')->put($image_name_original, $contents_original->encode());

        //     $contents_thumb->fit($imgUploadSizeThumbW, $imgUploadSizeThumbH, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        //     Storage::disk('affiliates')->put($image_name_thumb, $contents_thumb->encode());

        //     $image_paths = ['thumb' => $image_path_thumb, 'original' => $image_path_original];
        //     $affiliate->image = $image_paths;
        // }

        if($request->hasFile('image')) {
            $image = $request->file('image');

            $image_name_original = Utility::addUnderScore($affiliate->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::cleanString($image->getClientOriginalName());
            $image_name_thumb = Utility::addUnderScore($affiliate->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::cleanString($image->getClientOriginalName());

            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Affiliate::FILE_DIRECTORY);

            // $path = public_path('images/');
            !is_dir($destinationPath) &&
            mkdir($destinationPath, 0777, true);

            $image = ResizeImage::make($image->path());
            $image->resize($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function($constraint) {
                // $constraint->aspectRatio();
            });
            $image->save($destinationPath.'/'.$image_name_original,90);

            $image->resize($imgUploadSizeThumbW, $imgUploadSizeThumbH, function($constraint) {
                // $constraint->aspectRatio();
            });
            $image->save($destinationPath.'/'.$image_name_thumb,90);

            $image_paths = ['thumb' => $image_name_thumb, 'original' => $image_name_original];

            $affiliate->image = $image_paths;
        }

        $affiliate->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'New affiliate has been added successfully']);
        } else {
            return redirect()->route('admin.affiliates.index')->with('success', 'New affiliate has been added successfully');
        }
    }
    public function edit($id) {

        $user = User::findOrFail($id);

        /*$affiliate = Affiliate::findOrFail($id);*/

        $all_slug = AllSlug::where('causer_id',$user->affiliate->id)->where('causer_type', 'App\Models\Affiliate')->first();
        $slug = $all_slug->slug;
        return view('admin.pages.affiliates.add',['user'=>$user,'slug'=>$slug]);
    }
    public function update(Request $request, $id) {
        // $rules = [
        //     'name' => 'required',
        //     'email' => 'required',
        // ];
        // $messages = [
        //     'required' => 'The :attribute field is required.',
        // ];
        // $validator = Validator::make($request->all(), $rules, $messages);
        // if ($validator->fails()) {
        //     if($request->ajax()) {
        //         return response()->json($validator->errors(), 422);
        //     } else {
        //         return redirect()->route('admin.affiliates.edit',$id)
        //             ->withErrors($validator)
        //             ->withInput();
        //     }
        // }
        // else {

        // }
        $validated = request()->validate([
            'name' => 'required|max:255|unique:users,name,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        $user = User::findOrFail($id);

        $affiliate = Affiliate::where('user_id', $user->id)->first();

        $slug = new Slug();

        $all_slug = AllSlug::where('causer_id',$affiliate->id)->where('causer_type', 'App\Models\Affiliate')->first();
        if($all_slug) {
            if ($user->name != $request->name) {

                $slug_data = $slug->createSlug($request->name, $id);
                $all_slug->fill(['slug' => $slug_data])->save();
            }
        }else {
            $slug_data = $slug->createSlug($request->name);

            $all_slug = new AllSlug();
            $all_slug->fill([
                'causer_id' => $affiliate->id,
                'causer_type' => 'App\Models\Affiliate',
                'slug' => $slug_data,
            ]);
            $all_slug->save();
        }

        $user_input = $request->only(['name','email','username']);
        if(!empty($request->password)) {
            $user_input['password'] = bcrypt($request->password);
        }
        $user->fill($user_input)->save();

        $input = $request->only(['description','location','city','district','pin','contact_email','contact_phone','contact_whatsapp','footer_description','site_title','site_keywords','site_description']);
        $affiliate->fill($input)->save();

        $imgUploadSizeOriginal = Utility::IMAGE_AFFILIATE_ORIGINAL;
        $imgUploadSizeOriginalW = Utility::getImageDimension($imgUploadSizeOriginal)['width'];
        $imgUploadSizeOriginalH = Utility::getImageDimension($imgUploadSizeOriginal)['height'];

        $imgUploadSizeThumb = Utility::IMAGE_AFFILIATE_THUMB;
        $imgUploadSizeThumbW = Utility::getImageDimension($imgUploadSizeThumb)['width'];
        $imgUploadSizeThumbH = Utility::getImageDimension($imgUploadSizeThumb)['height'];

        if($request->hasFile('image')) {
            $this->destroy_image($affiliate->image['thumb']);
            $this->destroy_image($affiliate->image['original']);

            $image = $request->file('image');

            $image_name_original = Utility::addUnderScore($affiliate->id) . Utility::addUnderScore($imgUploadSizeOriginal) . Utility::cleanString($image->getClientOriginalName());
            $image_name_thumb = Utility::addUnderScore($affiliate->id) . Utility::addUnderScore($imgUploadSizeThumb) . Utility::cleanString($image->getClientOriginalName());

            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Affiliate::FILE_DIRECTORY);
            !is_dir($destinationPath) &&
            mkdir($destinationPath, 0777, true);

            $image = ResizeImage::make($image->path());
            $image->resize($imgUploadSizeOriginalW, $imgUploadSizeOriginalH, function($constraint) {
                // $constraint->aspectRatio();
            });
            $image->save($destinationPath.'/'.$image_name_original,90);

            $image->resize($imgUploadSizeThumbW, $imgUploadSizeThumbH, function($constraint) {
                // $constraint->aspectRatio();
            });
            $image->save($destinationPath.'/'.$image_name_thumb,90);

            $image_paths = ['thumb' => $image_name_thumb, 'original' => $image_name_original];
            $input['image'] = $image_paths;
        }
        else {
            if(isset($request->is_image) && ($request->is_image == 0)){
                $this->destroy_image($affiliate->image['thumb']);
                $this->destroy_image($affiliate->image['original']);

                $input['image'] = null;
            }
        }

        $affiliate->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'New affiliate has been added successfully']);
        } else {
            return redirect()->route('admin.affiliates.index')->with('success', 'New affiliate has been added successfully');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $affiliate = Affiliate::where('user_id', $id)->first();

        $all_slug = AllSlug::where('causer_id',$affiliate->id)->where('causer_type', 'App\Models\Affiliate')->first();
        $all_slug->delete();

        /*if(!empty($affiliate->image) && $affiliate->image != null){
            Storage::delete('public/' . $affiliate->image);
        }*/
        $this->destroy_image($affiliate->image['thumb']);
        $this->destroy_image($affiliate->image['original']);
        /*$affiliate->delete();*/
        $user->delete();

        return response()->json(['success' => 'Affiliate has been deleted successfully']);
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

        $model = Affiliate::where('user_id', $user->id)->first();
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public function destroy_image($image)
    {
        Storage::disk('public')->delete(Affiliate::FILE_DIRECTORY . '/'. $image);
        return 1;
    }

}
