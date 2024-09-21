<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class PinController extends Controller
{
    public  function index() {
        return view('admin.pages.stores.pin.index');
    }
    public  function data() {

        return Datatables::eloquent(Pin::select()->latest())
            ->rawColumns(['name','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#dvAdd-pin" data-target="' . route('admin.stores.pins.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.stores.pins.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.stores.pins.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('pin_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $returnHTML = view('admin.pages.stores.pin.add-modal-form')->render();
        return response()->json(['html' => $returnHTML]);
    }

    public  function store(Request $request) {
        $rules = [
            'name' => 'required|unique:pins'
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
            'unique' => 'PIN already exists',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.stores.pins.index')
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $pin = new Pin;
        $input['name'] = $request->name;
        $pin->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'New Pin added successfully']);
        } else {
            return redirect()->route('admin.stores.pins.index')->with(['success' => 'New Pin added successfully']);
        }
    }

    /*public function edit($id)
    {
        $pin = Pin::findOrFail($id);
        return response()->json(['pin' => $pin]);
    }*/

    public function edit_modal($id)
    {
        $pin = Pin::findOrFail($id);

        $returnHTML = view('admin.pages.stores.pin.add-modal-form',['pin' => $pin])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:pins,name,'.$id,
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
            'unique' => 'PIN already exists',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.stores.pins.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $pin = Pin::find($id);
        $input['name'] = $request->name;
        $pin->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Pin has been updated successfully']);
        } else {
            return redirect()->route('admin.stores.pins.index')->with(['success' => 'pin has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        Pin::find($id)->delete();
        return response()->json(['success' => 'Pin has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {

        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Pin::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
