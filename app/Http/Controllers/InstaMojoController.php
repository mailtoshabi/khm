<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstaMojoController extends Controller
{
    var $purpose;
    var $amount;
    var $email;
    var $phone;
    var $name;
    var $redirectUrl;

    public function __construct($amount,$purpose,$phone,$email,$name,$redirectUrl){
        $this->purpose=$purpose;
        $this->amount = $amount;
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
        $this->redirectUrl = $redirectUrl;
    }

    public function createRequest() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:90164dcc4aefb6b14b62736ce43f229f",
                "X-Auth-Token:219d171ea52f40f63847e2734f430004"));
        if(!empty($this->email)) {
            $payload = Array(
                'purpose' => $this->purpose,
                'amount' => $this->amount,
                'phone' => $this->phone,
                'buyer_name' => $this->name,
                'redirect_url' => $this->redirectUrl,
                'send_email' => false,
                'webhook' => route('checkout.payment.webhook'),
                'send_sms' => false,
                'email' => $this->email,
                'allow_repeated_payments' => false
            );
        }else {
            $payload = Array(
                'purpose' => $this->purpose,
                'amount' => $this->amount,
                'phone' => $this->phone,
                'buyer_name' => $this->name,
                'redirect_url' => $this->redirectUrl,
                'send_email' => false,
                'webhook' => route('checkout.payment.webhook'),
                'send_sms' => false,
                'email' => 'sales@keralahealthmart.com',
                'allow_repeated_payments' => false
            );
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        $data =  json_decode($response,true);
//        return response()->json($data['payment_request']['longurl']);
        return $data['payment_request']['longurl'];
    }
}
