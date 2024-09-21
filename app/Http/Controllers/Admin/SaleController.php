<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\front\PdfGenerateController;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\TypeProductPivot;
use App\Models\Website\CustomerDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Http\Utilities\Utility;
use Validator;
/*use App\Http\Controllers\SmsApiController as SmsApiController;*/
use App\Http\Controllers\SmsFastMsgController as SmsFastMsgController;

class SaleController extends Controller
{
    public  function index() {

        return view('admin.pages.sales.index');
    }
    public  function data(Request $request) {
        $sales = Sale::select(['id','order_no','pay_method','sub_total','is_paid','delivery_charge','delivery_type','status','is_cancelled_customer','utr_no','is_utr_cust','user_id','created_at'])->latest();
        return DataTables::of($sales)
            /*return Datatables::eloquent(Sale::select())*/
            ->filter(function ($query) use ($request) {
                if ($request->has('status') && !empty($request->status)) {
                    $query->where('status', $request->status);
                }
            })
            ->rawColumns(['order_no','pay_method','sub_total','status','payment','action'])
            ->editColumn('order_no', function ($modal) {
                $color = $modal->user->id == Utility::KHM_USER_ID ? '' : 'color: red';
                $href = $modal->user->id == Utility::KHM_USER_ID ? '#' : route('admin.affiliates.edit',$modal->user->id);
                $target = $modal->user->id == Utility::KHM_USER_ID ? '_self' : '_blank';
                $admin = $modal->user->id == Utility::KHM_USER_ID ? '(WEB ADMIN' : '';

                $data = '<p>' . $modal->created_at->format('d F, Y') . '</p>';
                $data .= '<p><strong>' . $modal->order_no . '</strong></p>';
                $data .= '<a href="' . $href . '" target="' . $target . '" style="' . $color . '"><strong>Order Through ' . $modal->user->name . '</strong> ' . $admin . '</p>';

                return $data;
            })
            ->editColumn('sub_total', function ($modal) {
                $total = $modal->sub_total+$modal->delivery_charge;
                $data = '<p>' . $total . '</p>';
                if($modal->delivery_charge!=0) {
                    $data .= '<p> <small>including delivery charge</small> Rs.' . $modal->delivery_charge . '</p>';
                }
                return $data;
                /*return '<p>' . $modal->sub_total . '</p>';*/
            })
            ->editColumn('status', function ($modal) {
                $data = '';
                if(($modal->pay_method == Utility::PAYMENT_OFFLINE) && (!empty($modal->utr_no)) && ($modal->is_utr_cust)) {
                    $data .= '<small class="label bg-green">UTR Updated By customer</small>';
                }
                if($modal->status == Utility::SALE_STATUS_CANCELLED) {
                   $post =  $modal->is_cancelled_customer ? 'By Customer' : 'By Admin';
                }else {
                    $post = '';
                }
                $data .= '<p>' . Utility::saleStatus()[$modal->status]. '' . $post . '</p>';
                return $data;
            })
            ->addColumn('payment', function ($modal) {
                $payment = $modal->is_paid ? '<small class="label bg-green">Paid</small>' : '<small class="label bg-red">Not Paid</small>';
                /*$method = $modal->pay_method == Utility::PAYMENT_COD ? '<small class="label label-primary">Cash on Delivery</small>' : '<small class="label label-primary">Online Payment</small>';*/
                if($modal->pay_method == Utility::PAYMENT_COD) {
                    $method = '<small class="label label-primary">Cash on Delivery</small>';
                }elseif($modal->pay_method == Utility::PAYMENT_ONLINE) {
                    $method = '<small class="label label-primary">Online Payment</small>';
                }elseif($modal->pay_method == Utility::PAYMENT_OFFLINE) {
                    $method = '<small class="label label-primary">Offline Payment</small>';
                }else {
                    $method = '<small class="label label-primary">Cash on Delivery</small>';
                }
                if($modal->delivery_type) {
                    $delivery_type = '<p><small class="label label-warning">To Pay Shipping</small></p>';
                }else {
                    $delivery_type = '<p><small class="label label-warning">Paid Shipping</small></p>';
                }

                return '<p>' . $payment . '</p>'.'<p>' . $method . '</p>' . $delivery_type;
            })
            ->addColumn('action', function ($modal) {
                return '<a  href="'. route('admin.sales.show',[$modal->id]) . '" title="View" > <i class="fa fa-eye text-primary"></i></a>';
            })
            ->setRowId('sale_{{$id}}')
            ->make(true);
    }

    public function show($id) {
        $sale = Sale::findOrFail($id);
        return view('admin.pages.sales.show',['sale'=>$sale]);
    }

    public function bill_download(Request $request, $id) {
        $sale = Sale::findOrFail($id);
        $customerDetails = $sale->customer->customer_detail;
        view()->share(['sale' => $sale, 'customerDetails' => $sale->customer->customer_detail]);

    //     if($request->has('download')){
    //         // Set extra option
    //         PDF::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
    //         // pass view file
    //         $pdf = PDF::loadview('admin.pages.pdfview');
    //         // download pdf
    // //            return $pdf->download('pdfview.pdf');
    //         return $pdf->stream();
    //     }

    //     return view('pdfview');

    if($request->has('download')){
        $filename = 'Invoice_#_' . Str::replace('/', '-', $sale->order_no) . '.pdf';
        $pdf = PDF::loadView('pdfview', compact('sale','customerDetails'));
    return $pdf->download($filename);
    }
    }

    public function change_status(Request $request) {
        $sale_id = $request->sale;
        $status = $request->status;
        $sale= Sale::find($sale_id);
        if($sale->status == Utility::SALE_STATUS_CANCELLED) {
            $canceler = $sale->is_cancelled_customer ? 'by customer' : 'by admin';
            return response()->json(['error' => 'Status already cancelled ' . $canceler,'is_paid'=>$sale->is_paid, 'status'=>Utility::saleStatus()[$sale->status], 'status_id'=>$sale->status, 'cancelor'=>$canceler]);
            //TODO: toast error is not working here (error toast coming in green color)
        }else {
            if ($sale->pay_method == Utility::PAYMENT_COD) {
                if ($status == Utility::SALE_STATUS_CLOSED) {
                    $sale->is_paid = 1;
                }
                else {
                    $sale->is_paid = 0;
                }
            }

            if ($status == Utility::SALE_STATUS_CANCELLED) {
                foreach ($sale->sale_details as $detail) {
                    $stock = Utility::get_stock($detail->product_id, $detail->type_size);
                    $new_stock = $stock + $detail->quantity;
                    $stock_details = TypeProductPivot::where('type_id', $detail->type_size)->where('product_id', $detail->product_id)->first();
                    $stock_details->stock = $new_stock;
                    $stock_details->save();
                }
                $canceler = 'by admin';
            } else {
                $canceler = '';
                if ($sale->status == Utility::SALE_STATUS_CANCELLED) {
                    // TODO : If one order cancelled it the status can't revert.
                }
            }
            $sale->status = $status;
            $sale->save();
            return response()->json(['success' => 'Status updated successfully','is_paid'=>$sale->is_paid, 'status'=>Utility::saleStatus()[$status], 'status_id'=>$status, 'cancelor'=>$canceler]);
        }
    }

    public function changePayment(Request $request) {
        $sale_id = $request->sale;
        $payment = $request->payment;
        $sale= Sale::find($sale_id);
        if($sale->status == Utility::SALE_STATUS_CANCELLED) {
            return response()->json(['error' => 'Something Went wrong']);
        }else {
            $sale->is_paid = $payment;
            $sale->save();
            return response()->json(['success' => 'Action done successfully', 'is_paid' => $payment]);
        }
    }

    public function edit_courier($id)
    {
        $sale = Sale::findOrFail($id);
        $returnHTML = view('admin.pages.sales.courier-modal-form',['sale' => $sale])->render();
        return response()->json(['html' => $returnHTML]);
    }

    public function update_courier(Request $request, $id)
    {
        $rules = [
            'courier' => 'required',
            'courier_track' => 'required',
            'delivery' => 'required',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.sales.courier.edit', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $sale = Sale::find($id);
        $input['courier'] = $request->courier;
        $input['courier_track'] = $request->courier_track;
        $input['delivery'] = $request->delivery;

        $sale->fill($input)->save();

        if(!empty($sale->customer->phone) && $request->has('is_couriersms')) {
            $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
            $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
            $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
            $smsText = config('app.name') . ': Your order ' . $sale->order_no . ' has been shipped and will be delivered by ' . $sale->delivery . ' days. Track your shipment in ' . Utility::courier()[$sale->courier]['website'] . ' with tracking code ' . $sale->courier_track;
            $mblno = Setting::IND_CODE . $sale->customer->phone;
            //$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');
            $sendSMS = $this->sendsms($sale->customer->phone, $smsText);
        }

        $sale->courier_name = Utility::courier()[$sale->courier]['name'];
        $sale->courier_website = Utility::courier()[$sale->courier]['website'];

        if ($request->ajax()) {
            return response()->json(['success' => 'Courier Data has been updated successfully', 'sale' => $sale]);
        } else {
            return redirect()->route('admin.categories.index')->with(['success' => 'Courier Data has been updated successfully']);
        }
    }

    public function sent_custom_sms(Request $request, $id)
    {
        $rules = [
            'sms_content' => 'required'
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('admin.sales.show', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
        }
        $sale = Sale::find($id);
        $input['sms_content'] = $request->sms_content;

        $sale->fill($input)->save();

        if(!empty($sale->customer->phone)) {
            $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
            $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
            $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
            $smsText = $request->sms_content;
            $mblno = Setting::IND_CODE . $sale->customer->phone;
            /*$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');*/
            $sendSMS = $this->sendsms($sale->customer->phone, $smsText);
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'SMS has been sent successfully', 'sale' => $sale]);
        } else {
            return redirect()->route('admin.categories.index')->with(['success' => 'SMS has been sent successfully']);
        }
    }

    /*public function sendsms($host,$username,$password,$sender, $message,$mobile,$msgtype,$dlr)
    {
        $sendsms = new SmsApiController($host,$username,$password,$sender, $message,$mobile,$msgtype,$dlr);
        return $sendsms->Submit();
    }*/

    public function sendsms($mobile, $message)
    {
        $sendsms = new SmsFastMsgController($mobile, $message);
        return $sendsms->Submit();
    }

    public function utr_update(Request $request) {
        $sale = Sale::find($request->sale_id);
        $input['utr_no'] = $request->utr_no;
        $input['is_paid'] = 1;
        $input['is_utr_cust'] = 0;
        $paid_status = 1;

        if(empty($request->utr_no)) {
            $input['is_paid'] = 0;
            $paid_status = 0;
        }

        $sale->fill($input)->save();

        return response()->json(['utr' => $request->utr_no, 'paid_status' => $paid_status, 'success' => 'UTR number has been updated successfully']);
    }
}
