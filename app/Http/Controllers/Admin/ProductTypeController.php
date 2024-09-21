<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ProductTypeController extends Controller
{
    public  function index() {
        return view('admin.pages.products.types');
    }
    public  function data() {

        return Datatables::eloquent(ProductType::select())
            ->rawColumns(['name','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->name . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';
                return '<a data-plugin="render-modal" data-modal="#myTypeSizeModal" data-target="' . route('admin.products.types.edit_modal',[$modal->id]) . '" title="Edit" > <i class="fa fa-pencil text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.products.types.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.products.types.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>';
            })
            ->setRowId('product_type_{{$id}}')
            ->make(true);
    }

    public function create_modal()
    {
        $returnHTML = view('admin.pages.products.type-size-modal-content')->render();
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
                return redirect()->route('admin.lab.customers.create')
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $category = new ProductType;
        $input['name'] = $request->name;
        $category->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'New Product Type added successfully']);
        } else {
            return redirect()->route('admin.products.types.index')->with(['success' => 'New Product Type added successfully']);
        }
    }

    public function edit_modal($id)
    {
        $type = ProductType::findOrFail($id);
        $returnHTML = view('admin.pages.products.type-size-modal-content',['type' => $type])->render();
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
                return redirect()->route('admin.categories.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $category = ProductType::find($id);
        $input['name'] = $request->name;

        $category->fill($input)->save();

        if ($request->ajax()) {
            return response()->json(['success' => 'Product Type has been updated successfully']);
        } else {
            return redirect()->route('admin.products.types.index')->with(['success' => 'Product Type has been updated successfully']);
        }
    }

    public function destroy($id)
    {
        ProductType::find($id)->delete();
        return response()->json(['success' => 'Product Type has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {

        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = ProductType::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }

        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
