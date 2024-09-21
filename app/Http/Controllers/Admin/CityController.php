<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Image;
use Storage;
use App\Http\Utilities\Utility;

class CityController extends Controller
{
    public  function index() {
        return view('admin.pages.cities.index');
    }
    public  function data() {

        return Datatables::eloquent(City::select()->oldest())
            ->rawColumns(['name','district_id','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->editColumn('district_id', function ($modal) {
                return '<p>' . Utility::district_name($modal->district_id) . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-city" data-target="' . route('admin.cities.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.cities.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.cities.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('city_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $districts = DB::table('districts')->where('state_id',Utility::STATE_ID_KERALA)->pluck('name','id');
        $returnHTML = view('admin.pages.cities.add-modal-form',['districts'=>$districts])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public  function store(Request $request) {
        $rules = [
            'district_id' => 'required',
            'name' => 'required|unique:cities'
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return '';
            }
        }
        $city = new City;
        $input['name'] = $request->name;
        $input['district_id'] = $request->district_id;
        $city->fill($input)->save();
        if ($request->ajax()) {
            return response()->json(['success' => 'New City added successfully']);
        } else {
            return redirect()->route('admin.cities.index')->with(['success' => 'New City added successfully']);
        }
    }

    public function edit($id)
    {
        $city = City::findOrFail($id);

        return response()->json(['city' => $city]);
    }

    public function edit_modal($id)
    {
        $city = City::findOrFail($id);
        $districts = DB::table('districts')->where('state_id',Utility::STATE_ID_KERALA)->pluck('name','id');
        $returnHTML = view('admin.pages.cities.add-modal-form',['city' => $city,'districts' => $districts])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'district_id' => 'required',
            'name' => 'required|unique:cities,name,'.$id
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.cities.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $city = City::find($id);
        $input['name'] = $request->name;
        $input['district_id'] = $request->district_id;
        $city->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'City has been updated successfully']);
        } else {
            return redirect()->route('admin.cities.index')->with(['success' => 'City has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        City::find($id)->delete();
        return response()->json(['success' => 'City has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = City::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
