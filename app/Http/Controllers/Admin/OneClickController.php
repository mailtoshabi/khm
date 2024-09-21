<?php

namespace App\Http\Controllers\Admin;

use App\Models\OneclickPurchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use App\Http\Utilities\Utility;

class OneClickController extends Controller
{
    public  function index() {
        return view('admin.pages.oneclicks.index');
    }
    public  function data() {

        return Datatables::eloquent(OneclickPurchase::select()->orderBy('is_active','desc')->latest())
            ->rawColumns(['phone','product_id','user_id','is_active','created_at','action'])
            ->editColumn('created_at', function ($modal) {
                $data = '<p>' . $modal->created_at->format('d-m-Y') . '</p>';
                return $data;
            })
            ->editColumn('phone', function ($modal) {
                $data = '<p>' . $modal->phone . '</p>';
                return $data;
            })
            ->editColumn('product_id', function ($modal) {
                $data = '<p>' . $modal->product->name . '</p>';
                return $data;
            })
            ->editColumn('user_id', function ($modal) {
                $color = $modal->user->id == Utility::KHM_USER_ID ? '' : 'color: red';
                $href = $modal->user->id == Utility::KHM_USER_ID ? '#' : route('admin.affiliates.edit',$modal->user->id);
                $target = $modal->user->id == Utility::KHM_USER_ID ? '_self' : '_blank';
                $admin = $modal->user->id == Utility::KHM_USER_ID ? '(WEB ADMIN)' : '';
                return '<a href="' . $href . '" target="' . $target . '" style="' . $color . '"><strong>Order Through ' . $modal->user->name . '</strong> ' . $admin . '</p>';
            })
            ->editColumn('is_active', function ($modal) {
                return $modal->is_active==1 ? 'New' : 'Closed';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-minus' : 'fa-check';
                $publishTitle = $modal->is_active==1 ? 'New Sale' : 'Sale Closed';
                return '<a data-action="' . route('admin.oneclick_purchase.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>
                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.oneclick_purchase.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>';
            })
            ->setRowId('oneclick_{{$id}}')
            ->make(true);
    }

    public function destroy($id)
    {
        OneclickPurchase::find($id)->delete();
        return response()->json(['success' => 'Entry has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = OneclickPurchase::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
