<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhonePeController extends Controller
{
  public function phonepe() {
    $data = [
      "merchantId" => "MERCHANTUAT",
      "merchantTransactionId" => "MT7850590068188104",
      "merchantUserId" => "MU933037302229373",
      "amount" => 10000,
      "redirectUrl" => route('phonepe.response'),
      "callbackUrl" => route('phonepe.response'),
      "mobileNumber" => "9999999999",
      "paymentInstrument" => [
        "type" => "PAY_PAGE",
      //  "targetApp" => "com.phonepe.app",
      ],
    ];
    $encode = base64_encode(json_encode($data));

    $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
    $saltIndex = 1;
    $string = $encode. '/pg/v1/pay' . $saltKey;
    $sha256 = hash('sha256',$string);
    $finalXHeader = $sha256. '###' . $saltIndex;

    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'request' => $encode
      ]),
      CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "X-VERIFY: ".$finalXHeader,
        "accept: application/json"
      ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      // echo "cURL Error #:" . $err;
      return redirect()->route('phonepe.failed')->with(['errorMessage'=>$err]);
      
    } else {
      // echo $response;
      $responseData = json_decode($response,true);
      // dd($responseData);
      $url = $responseData['data']['instrumentResponse']['redirectInfo']['url'];
      return redirect()->to($url);
    }
  }

  public function response (Request $request) {
      $input = $request->all();
      dd($input);
      // $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
      // $saltIndex = 1;
      // $finalXHeader = hash('sha256','/pg/v1/status/'.$input['merchantId'].'/'.$input['transactionId'].$saltKey) . '###' . $saltIndex;

      // $curl = curl_init();

      // curl_setopt_array($curl, [
      //   CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/".$input['merchantId'].'/'.$input['transactionId'],
      //   CURLOPT_RETURNTRANSFER => true,
      //   CURLOPT_ENCODING => "",
      //   CURLOPT_MAXREDIRS => 10,
      //   CURLOPT_TIMEOUT => 30,
      //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      //   CURLOPT_CUSTOMREQUEST => "GET",
      //   CURLOPT_HTTPHEADER => [
      //     "Content-Type: application/json",
      //     "X-VERIFY: " . $finalXHeader,
      //     "X-MERCHANT-ID: " . $input['transactionId'],
      //     "accept: application/json"
      //   ],
      // ]);

      // $response = curl_exec($curl);
      // $err = curl_error($curl);

      // curl_close($curl);

      // if ($err) {
      //   return redirect()->route('phonepe.failed')->with(['errorMessage'=>$err]);
      // } else {
      //   return $response;
      // }
  }

}
