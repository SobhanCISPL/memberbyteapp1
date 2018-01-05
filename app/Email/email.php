<?php

namespace App\Email;
use Illuminate\Support\Facades\Mail;

final class Email {
	public function sendEmail ($email='',$data=[]) {
		try{

			Mail::send('email.otp', $data, function($message) use ($email) {
                $message->from('sobhan.das@documentscanner.in','Code Clouds Developer');

                $message->to($email)->subject('Login OTP!');           
            });
            return 1;

		}catch(\Exception $e){

		    return response()->json(['code'=>500,'message'=>'error']);
		}
	}
}

?>