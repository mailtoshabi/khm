<?php

namespace App\Http\Controllers\CustomerAuth;

use  App\Models\Category;
use App\Http\Utilities\Utility;
use  App\Models\Sale;
use  App\Models\Setting;
use  App\Models\Website\CustomerDetail;
use Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use  App\Models\Website\Customer;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Fpdf;
/*use App\Http\Controllers\SmsApiController as SmsApiController;*/
use App\Http\Controllers\SmsFastMsgController as SmsFastMsgController;
use App\Mail\SendOtp;

class RegisterController extends Controller
{

    use RegistersUsers;

    protected $redirectPath = '/';

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        //TODO : if user click cancel after receiving OTP
        //Validates data
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            if($request->ajax()) {
                return response()->json($validator->errors(), 422);
            } else {
                return redirect()->route('index')
                    ->withErrors($validator)
                    ->withInput();
            }
        }else {
            //Create customer
            if(!empty($request->phone)) {  //If have phone
                $customer = Customer::where('phone', $request->phone)->first();
                if($customer) {
                    $this->generate_otp($customer);
                }else {
                    $customer = $this->create($request->all());
                    $this->generate_otp($customer);
                }
                return ['data'=>$customer];
            }

        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            /*'email' => 'required|email|max:255|unique:customers',*/
            /*'email' => [
                'bail',
                'required',
                'email',
                'max:255',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('status', '!=', 0);
                }),
            ],
            'phone' => [
                'bail',
                'required',
                'min:10',
                'max:10',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('status', '!=', 0);
                }),
            ],*/
            'phone' => 'required|min:10|max:10|unique:customers',
            'password' => 'required|min:6|confirmed'
        ]);
        //TODO : validation - email or phone anyone should make mandatory
    }

    protected function create(array $data)
    {
        $customer = new Customer;

        $customer->fill([
            /*'email' => $data['email'],*/
            'phone' => $data['phone'],
            'password' => bcrypt($data['password'])
        ]);
        $customer->save();

        $customer_detail = new CustomerDetail;
        $customer_detail->fill([
            'customer_id' => $customer->id,
            'address' => [],
            'profile_pic' => '',
        ]);
        $customer_detail->save();

        return $customer;
    }

    protected function generate_otp($customer) {
        $otp = Utility::otp();
        session(['kerala_h_m_o_t_p' => $otp]);
        session(['kerala_h_m_cust_temp' => $customer->id]);
        session(['kerala_h_m_cust_phone' => $customer->phone]);
        if(!empty($customer->email)) {
            Mail::to($customer->email)->send(new SendOtp());
        }
        if(!empty($customer->phone)) {
            $smsApiUser = Setting::where('term', 'smsapi_user')->value('value');
            $smsApiPass = Setting::where('term', 'smsapi_password')->value('value');
            $smsApiSender = Setting::where('term', 'smsapi_sender')->value('value');
            $smsText = 'Use ' . $otp . ' as OTP for Verification of your customer account in ' . config('app.domain');
            $mblno = Setting::IND_CODE . $customer->phone;
            //$sendSMS = $this->sendsms(Setting::SERVER_IP, Setting::USER_PREFIX . $smsApiUser, $smsApiPass, $smsApiSender, $smsText, $mblno, '0', '1');
            $sendSMS = $this->sendsms($customer->phone, $smsText);
        }
    }

    protected function generate_future_otp(Request $request) {
        $user = $request->input_username;

        if (filter_var($user, FILTER_VALIDATE_EMAIL)) {
            $customer = Customer::where('email',$user)->first();
        }else {
            $customer = Customer::where('phone',$user)->first();
        }
        $this->generate_otp($customer);

        /*$otp = Utility::otp();
        session(['kerala_h_m_o_t_p' => $otp]);
        session(['kerala_h_m_cust_temp' => $customer->id]);
        session(['kerala_h_m_cust_phone' => $customer->phone]);
        if(!empty($customer->email)) {
            Mail::to($customer->email)->send(new SendOtp());
        }
        if(!empty($customer->phone)) {

        }*/
    }

    public  function resend_otp() {

    }

    public  function validate_otp(Request $request) {

        $validator = Validator::make($request->all(), [
            'khm_otp' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        else {
            $otp = $request->session()->get('kerala_h_m_o_t_p');
            $get_otp = (int)$request->khm_otp;
            if($otp == $get_otp) {
                $customer_id = $request->session()->get('kerala_h_m_cust_temp');
                $customer = Customer::find($customer_id);

                $customer->fill([
                    'status' => 1,
                    'is_active' => 1
                ]);
                $customer->save();
                //TODO: destroy necessary session variables like otp etc..

                //Authenticates customer
                $this->guard()->login($customer);
                //Redirects customer
//                return redirect($this->redirectPath);
                return response()->json();
            }else {
                $validator->getMessageBag()->add('khm_otp', 'Incorrect OTP Entered..');
                if ($request->ajax()) {
                    return response()->json($validator->errors(), 422);
                } else {
                    return redirect()->route('index')
                        ->withErrors($validator->errors());
                }


            }
        }


    }

    protected function guard()
    {
        return Auth::guard('customer');
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


}
