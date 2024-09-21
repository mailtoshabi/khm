<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image as ResizeImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Utilities\Utility;

class SliderController extends Controller
{
    public  function index() {
        return view('admin.pages.sliders.index');
    }
    public  function data() {

        return Datatables::eloquent(Slider::select()->orderBy('order_no','asc'))
            ->rawColumns(['type','order_no','image','action'])
            ->editColumn('type', function ($modal) {
                return Utility::slider_type()[$modal->type];
            })
            ->editColumn('order_no', function ($modal) {
                return $modal->order_no;
            })
            ->editColumn('image', function ($modal) {
                return '<img class="tbl-banner-image" src="' . asset(Utility::DEFAULT_STORAGE . Slider::FILE_DIRECTORY .  '/' . $modal->image) . '">' ;
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-slider" data-target="' . route('admin.sliders.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.sliders.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.sliders.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('slider_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $returnHTML = view('admin.pages.sliders.add-modal-form')->render();
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
                return redirect()->route('admin.sliders.create')
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $slider = new Slider;
        $input['type'] = $request->type;
        $input['image'] = '';
        $input['order_no'] = empty($request->order_no) ? Utility::DEFAULT_DB_ORDER : $request->order_no;
        $slider->fill($input)->save();

        if(request()->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Slider::FILE_DIRECTORY);
            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);
            $file_name = Utility::addUnderScore($slider->id) . Utility::cleanString($file->getClientOriginalName());
            request('image')->storeAs('public' . DIRECTORY_SEPARATOR . Slider::FILE_DIRECTORY, $file_name);
            $slider->image = $file_name;
            $slider->save();
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'New Slider added successfully']);
        } else {
            return redirect()->route('admin.sliders.index')->with(['success' => 'New Slider added successfully']);
        }
    }

    public function edit($id)
    {
        /*$slider = Slider::findOrFail($id);
        return response()->json(['slider' => $slider]);*/
    }

    public function edit_modal($id)
    {
        $slider = Slider::findOrFail($id);
        $returnHTML = view('admin.pages.sliders.add-modal-form',['slider' => $slider])->render();
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
                return redirect()->route('admin.sliders.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $slider = Slider::find($id);
        $input['type'] = $request->type;
        $input['order_no'] = empty($request->order_no) ? Utility::DEFAULT_DB_ORDER : $request->order_no;

        if(request('isImageDelete')==0) {
            $this->destroy_image($slider->image);
            // $input['image'] =null;
        }
        if(request()->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = storage_path("app" . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Slider::FILE_DIRECTORY);
            !is_dir($destinationPath) &&
                mkdir($destinationPath, 0777, true);
            $file_name = Utility::addUnderScore($slider->id) . Utility::cleanString($file->getClientOriginalName());
            request('image')->storeAs('public' . DIRECTORY_SEPARATOR . Slider::FILE_DIRECTORY, $file_name);
            $slider->image = $file_name;
            $slider->save();
        }

        $slider->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Slider has been updated successfully']);
        } else {
            return redirect()->route('admin.sliders.index')->with(['success' => 'slider has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        $slider = Slider::find($id);
        if(!empty($slider->image) && $slider->image != null){
            Storage::delete('public/' . $slider->image);
        }
        $slider->delete();
        return response()->json(['success' => 'Slider has been deleted successfully']);
    }

    public function destroy_image($image)
    {
        if(!empty($image) && $image != null){
            Storage::disk('public')->delete(Slider::FILE_DIRECTORY . '/'. $image);
        }
        return 1;
    }

    public function change_status(Request $request, $id)
    {

        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Slider::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
