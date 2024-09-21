<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use File;
use Storage;
use Image;
use PDF;

class ClinicController extends Controller
{
    public  function index() {

        return view('pages.clinic_profile');
    }

}
