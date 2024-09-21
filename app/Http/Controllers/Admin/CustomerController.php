<?php

namespace App\Http\Controllers\Admin;

use App\Models\Website\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public  function index() {

        return view('admin.pages.customers.index');
    }
    public  function data() {
        $sales = Customer::select(['id','email','phone','is_active','is_access','created_at'])->latest();
        return DataTables::of($sales)
            /*return Datatables::eloquent(Sale::select())*/
            ->rawColumns(['name','email','phone','action'])
            ->editColumn('name', function ($modal) {
                return '<p>' . $modal->customer_detail->name . '</p>';
            })
            ->editColumn('email', function ($modal) {
                return '<p>' . $modal->email . '</p>';
            })
            ->editColumn('phone', function ($modal) {
                return '<p>' . $modal->phone . '</p>';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-power-off' : 'fa-circle-o-notch';
                $publishTitle = $modal->is_active==1 ? 'Unpublish' : 'Publish';

                $publishIcon_acs = $modal->is_access==1 ? 'fa-minus' : 'fa-check';
                $publishTitle_acs = $modal->is_access==1 ? 'Quantity Discount Access Deativate' : 'Quantity Discount Access Ativate';
                return '<a data-action="' . route('admin.customers.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.customers.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.customers.change_access',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_access . '" title="'. $publishTitle_acs . '"> <i class="fa '. $publishIcon_acs . ' text-primary"></i></a>';
            })
            ->setRowId('customer_{{$id}}')
            ->make(true);
    }

    public function destroy($id)
    {
        Customer::find($id)->delete();
        return response()->json(['success' => 'Customer has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Customer::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }

    public function change_access(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'removed' : 'activated';
        $model = Customer::find($id);
        if($model) {
            $model->is_access = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Access to quatity discount has been ' . $new_status . ' successfully']);
    }

}
