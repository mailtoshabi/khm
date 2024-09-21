<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Utility;

class SmsFastMsgController extends Controller
{

    /*
    * Username that is to be used for submission
    */
    var $strAuthKey;
    /*
    * Message content that is to be transmitted
    */
    var $strMessage;
    /*
    * Sender Id to be used for submitting the message
    */
    var $strSender;

    var $strRouteId;
    /*
    * Mobile No is to be transmitted.
    */
    var $strMobile;


//Constructor..
    public function __construct($mobile, $message){
        $this->strAuthKey = Utility::settings('smsapi_user');
        $this->strMessage= $message; //Utility::unicodeString($message)
        $this->strSender= Utility::settings('smsapi_sender');
        $this->strRouteId= 8;
        /*
         1 = Transactional Route, 2 = Promotional Route, 3 = Trans DND Route, 7 = Transcrub Route, 8 = OTP Route, 9 = Trans Stock Route, 10 = Trans Property Route, 11 = Trans DND Other Route, 12 = TransCrub Stock, 13 = TransCrub Property, 14 = Trans Crub Route.
         */
        $this->strMobile=$mobile;
    }
    public function Submit(){

        //API URL

        $url= "http://msg.msgclub.net/rest/services/sendSMS/sendGroupSms?AUTH_KEY=". $this->strAuthKey . "&message=" . urlencode($this->strMessage) . "&senderId=" . $this->strSender . "&routeId=" . $this->strRouteId . "&mobileNos=" . $this->strMobile . "&smsContentType=english";

        // init the resource

        $ch = curl_init();

        curl_setopt_array($ch, array(

            CURLOPT_URL => $url,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_SSL_VERIFYHOST => 0,

            CURLOPT_SSL_VERIFYPEER => 0

        ));

        //get response

        $output = curl_exec($ch);

        //Print error if any

        if(curl_errno($ch))

        {
            echo 'error:' . curl_error($ch);
        }


        curl_close($ch);


        //return $output;
        return true;
    }
}
