<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtp extends Mailable
{
    use Queueable, SerializesModels;
    /*public $userName;*/

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*$this->userName=$userName;*/
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = 'sales@keralahealthmart.com'; //
        $name = config('app.name'); //
        $subject = 'One Time Password for '. $name;
        /*$attchFile = "http://localhost/goeez-v1/public/mailattachments/01_Apartment_Hunting_Service_Charges.pdf";
            ->attach($attchFile, [
                'as' => '01_Apartment_Hunting_Service_Charges.pdf',
                'mime' => 'application/pdf',
            ]);*/

        return $this->view('mails.otp')
            ->from($from, $name) //TODO : check by removing this area and five from email/name globally in config/mail.php
            ->replyTo($from, $name)
            ->subject($subject);
    }
}
