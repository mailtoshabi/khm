<?php

namespace App\Http\Controllers\front;

use App\Models\Sale;
use App\Models\Website\CustomerDetail;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PdfGenerateController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function pdfview(Request $request)
    {
        $customer_id = Auth::guard('customer')->user()->id;
        $customerDetails = CustomerDetail::with('customer')->find($customer_id);

        $last_sale = Sale::where('customer_id',$customer_id)->latest()->first();


        view()->share(['sale' => $last_sale, 'customerDetails' => $last_sale->customer->customer_detail]);

        if($request->has('download')){
            // Set extra option
            PDF::setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);
            // pass view file
            $pdf = PDF::loadView('pdfview')->setPaper('a4', 'portrait'); //portrait
//            PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
            // download pdf
//            return $pdf->download('pdfview.pdf');
            return $pdf->stream();
        }

        return view('pdfview');
    }

    public function viewBill(Request $request,$id)
    {
        $customer_id = Auth::guard('customer')->user()->id;
        /*$customerDetails = CustomerDetail::with('customer')->find($customer_id);*/

        $sale = Sale::find($id);
        $customerDetails = $sale->customer->customer_detail;

        // view()->share(['sale' => $sale, 'customerDetails' => $sale->customer->customer_detail]);

        if($request->has('download')){
            // Set extra option
            // PDF::setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);
            // pass view file
            // $pdf = PDF::loadView('pdfview')->setPaper('a4', 'landscape'); //portrait

            // return $pdf->stream();

            $filename = 'Invoice_#_' . Str::replace('/', '-', $sale->order_no) . '.pdf';
            $pdf = PDF::loadView('pdfview', compact('sale','customerDetails'));
        return $pdf->download($filename);
        }

        // return view('pdfview');
    }
}
