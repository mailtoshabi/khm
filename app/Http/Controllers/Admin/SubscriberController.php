<?php

namespace App\Http\Controllers\Admin;


use App\Models\Subscribe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class SubscriberController extends Controller
{
    public  function index() {
        return view('admin.pages.subscriber.index');
    }
    public  function data() {

        return Datatables::eloquent(Subscribe::select())
            ->rawColumns(['phone'])
            ->editColumn('phone', function ($modal) {
                return '<p>' . $modal->phone . '</p>';
            })
            ->setRowId('subscriber_{{$id}}')
            ->make(true);
    }
}
