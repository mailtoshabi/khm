<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Http\Utilities\Utility;
use Illuminate\Support\Facades\Validator as Validator;
use Intervention\Image\Facades\Image as ResizeImage;

class BannerController extends Controller
{
    public  function index() {
        return view('admin.pages.banners.index');
    }
    public  function data() {

        return Datatables::eloquent(Banner::select()->orderBy('order_no','asc'))
            ->rawColumns(['link','image','action'])
            ->editColumn('link', function ($modal) {
                $user = $modal->user_id == Utility::KHM_USER_ID ? 'Admin' : 'User';
                $data = !empty($modal->link) ? '<p>' . $modal->link . '</p>' : '--';
                $data .= !empty($modal->user_id) ? '<p class="text-primary">Added to ' . $user . ' : ' . $modal->affiliate->name . '</p>' : '--';
                return $data;
            })
            ->editColumn('image', function ($modal) {
                return '<img class="tbl-banner-image" src="' . asset(Utility::DEFAULT_STORAGE . Banner::FILE_DIRECTORY .  '/' . $modal->image) . '">' ;
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-banner" data-target="' . route('admin.banners.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.banners.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.banners.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('banner_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $users = User::whereHas('roles' , function($q){
            $q->where('id', Utility::AFFILIATE_ROLE_ID);
        })->pluck('name','id');

        $returnHTML = view('admin.pages.banners.add-modal-form',['users'=>$users])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public  function store(Request $request) {
        $rules = [

        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.banners.create')
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $banner = new Banner;
        $input['user_id'] = $request->user_id;
        $input['link'] = $request->link;
        $input['image'] = '';
        $input['order_no'] = empty($request->order_no) ? Utility::DEFAULT_DB_ORDER : $request->order_no;
        $banner->fill($input)->save();

        if(request()->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Banner::FILE_DIRECTORY);
            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);
            $file_name = Utility::addUnderScore($banner->id) . Utility::cleanString($file->getClientOriginalName());
            request('image')->storeAs('public' . DIRECTORY_SEPARATOR . Banner::FILE_DIRECTORY, $file_name);
            $banner->image = $file_name;
            $banner->save();
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'New Banner added successfully']);
        } else {
            return redirect()->route('admin.banners.index')->with(['success' => 'New Banner added successfully']);
        }
    }

    public function edit($id)
    {
        /*$banner = Banner::findOrFail($id);
        return response()->json(['banner' => $banner]);*/
    }

    public function edit_modal($id)
    {
        $users = User::where('id','!=',Utility::CMS_USER_ID)->pluck('name','id');
        $banner = Banner::findOrFail($id);
        $returnHTML = view('admin.pages.banners.add-modal-form',['banner' => $banner,'users' => $users])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public function update(Request $request, $id)
    {
        $rules = [

        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.banners.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $banner = Banner::find($id);
        $input['user_id'] = $request->user_id;
        $input['link'] = $request->link;
        $input['order_no'] = empty($request->order_no) ? Utility::DEFAULT_DB_ORDER : $request->order_no;


        if(request('isImageDelete')==0) {
            $this->destroy_image($banner->image);
            // $input['image'] =null;
        }
        if(request()->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Banner::FILE_DIRECTORY);
            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);
            $file_name = Utility::addUnderScore($banner->id) . Utility::cleanString($file->getClientOriginalName());
            request('image')->storeAs('public' . DIRECTORY_SEPARATOR . Banner::FILE_DIRECTORY, $file_name);
            $banner->image = $file_name;
            $banner->save();
        }

        $banner->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Banner has been updated successfully']);
        } else {
            return redirect()->route('admin.banners.index')->with(['success' => 'banner has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        $banner = Banner::find($id);
        if(!empty($banner->image) && $banner->image != null){
            Storage::disk('public')->delete(Banner::FILE_DIRECTORY . '/'. $banner->image);
        }
        $banner->delete();
        return response()->json(['success' => 'Banner has been deleted successfully']);
    }

    public function destroy_image($image)
    {
        if(!empty($image) && $image != null){
            Storage::disk('public')->delete(Banner::FILE_DIRECTORY . '/'. $image);
        }
        return 1;
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Banner::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
