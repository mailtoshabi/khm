<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClinicType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Image;
use Storage;
use App\Http\Utilities\Utility;

class ClinicTypeController extends Controller
{
    public  function index() {
        return view('admin.pages.clinics.types.index');
    }
    public  function data() {

        return Datatables::eloquent(ClinicType::select()->oldest())
            ->rawColumns(['name','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-clinic_type" data-target="' . route('admin.clinics.types.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.clinics.types.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.clinics.types.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('clinic_type_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $returnHTML = view('admin.pages.clinics.types.add-modal-form')->render();
        return response()->json(['html' => $returnHTML]);
    }

    public  function store(Request $request) {
        $rules = [
            'name' => 'required'
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
        $clinic_type = new ClinicType;
        $input['name'] = $request->name;
        $clinic_type->fill($input)->save();
        if ($request->ajax()) {
            return response()->json(['success' => 'New Clinic Type added successfully']);
        } else {
            return redirect()->route('admin.clinics.types.index')->with(['success' => 'New Clinic Type added successfully']);
        }
    }

    public function edit($id)
    {
        $clinic_type = ClinicType::findOrFail($id);

        return response()->json(['clinic_type' => $clinic_type]);
    }

    public function edit_modal($id)
    {
        $clinic_type = ClinicType::findOrFail($id);
        $returnHTML = view('admin.pages.clinics.types.add-modal-form',['clinic_type' => $clinic_type])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required'
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.clinics.types.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $clinic_type = ClinicType::find($id);
        $input['name'] = $request->name;
        $clinic_type->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Clinic Type has been updated successfully']);
        } else {
            return redirect()->route('admin.clinics.types.index')->with(['success' => 'Clinic Type has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        ClinicType::find($id)->delete();
        return response()->json(['success' => 'Clinic Type has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {

        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = ClinicType::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
