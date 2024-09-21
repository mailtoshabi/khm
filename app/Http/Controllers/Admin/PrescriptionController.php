<?php

namespace App\Http\Controllers\Admin;

use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use App\Http\Utilities\Utility;

class PrescriptionController extends Controller
{
    public  function index() {
        return view('admin.pages.prescriptions.index');
    }
    public  function data() {

        return Datatables::eloquent(Prescription::select()->orderBy('is_active','desc')->latest())
            ->rawColumns(['phone_prescription','image_prescription','user_id','is_active','created_at','action'])
            ->editColumn('created_at', function ($modal) {
                $data = '<p>' . $modal->created_at->format('d-m-Y') . '</p>';
                return $data;
            })
            ->editColumn('phone_prescription', function ($modal) {
                $data = '<p>' . $modal->phone_prescription . '</p>';
                return $data;
            })
            ->editColumn('image_prescription', function ($modal) {
                $main_image = !empty($modal->image_prescription) ? '<img src="' . asset($modal->image_prescription) . '" alt="" height="50" />' : '';
                $link = !empty($modal->image_prescription) ? '<a target="_blank" href="' . asset($modal->image_prescription) . '" >View</a>' : '';
                return '<p>' . $main_image . '</p><p> ' . $link . '</p>';
            })
            ->editColumn('user_id', function ($modal) {
                $color = $modal->user->id == Utility::KHM_USER_ID ? '' : 'color: red';
                $href = $modal->user->id == Utility::KHM_USER_ID ? '#' : route('admin.affiliates.edit',$modal->user->id);
                $target = $modal->user->id == Utility::KHM_USER_ID ? '_self' : '_blank';
                $admin = $modal->user->id == Utility::KHM_USER_ID ? '(WEB ADMIN)' : '';
                return '<a href="' . $href . '" target="' . $target . '" style="' . $color . '"><strong> ' . $modal->user->name . '</strong> ' . $admin . '</p>';
            })
            ->editColumn('is_active', function ($modal) {
                return $modal->is_active==1 ? 'New' : 'Closed';
            })
            ->addColumn('action', function ($modal) {
                $publishIcon = $modal->is_active==1 ? 'fa-minus' : 'fa-check';
                $publishTitle = $modal->is_active==1 ? 'New' : 'Closed';
                return '<a data-action="' . route('admin.prescriptions.change_status',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-type="GET" data-formdata="' . $modal->is_active . '" title="'. $publishTitle . '"> <i class="fa '. $publishIcon . ' text-primary"></i></a>
                &nbsp;&nbsp;&nbsp;&nbsp;<a data-action="' . route('admin.prescriptions.delete',[$modal->id]) . '" href="" data-plugin="ajaxGetRequest" data-conf-message="Are you sure to delete..?" data-type="DELETE" title="Delete"> <i class="fa fa-trash text-primary"></i></a>';
            })
            ->setRowId('prescription_{{$id}}')
            ->make(true);
    }

    public function destroy($id)
    {
        Prescription::find($id)->delete();
        return response()->json(['success' => 'Entry has been deleted successfully']);
    }

    public function change_status(Request $request, $id)
    {
        $changeStatus = $request->value == 1 ? 0 : 1;
        $new_status = $request->value == 1 ? 'inactive' : 'active';
        $model = Prescription::find($id);
        if($model) {
            $model->is_active = $changeStatus;
            $model->save();
        }
        return response()->json(['success' => 'Status has been changed to ' . $new_status . ' successfully']);
    }
}
