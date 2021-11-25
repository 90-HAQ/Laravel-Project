<?php
namespace App\Services;
use App\Jobs\SendEmailJob;

class Email_Service
{
    // mail sending function
    public function sendmail($sendto, $verify_token)
    {
        $details = [
            'title' =>  'Signup Verification.',
            'body'  =>  'Please Verify your Account. Please Click on this link to verify http://127.0.0.1:8000/api/welcome_login'.'/'.$sendto.'/'.$verify_token
        ];

        // queue to mail job object and function 
        
        //$email = new SendEmailJob($sendto, $details);
        dispatch(new SendEmailJob($sendto, $details));
        //$email->handle();

        // Mail::to($sendto)->send(new testmail($details));
        return response()->json(['Message' => 'Email has been sent for Verification, Please verify your Account.']);
    }


    // send token as otp for resetting old password with new password,
    function sendMailForgetPassword($mail,$otp)
    {
        $details=[
            'title'=> 'Forget Password Verification',
            'body'=> 'Your OTP is '. $otp . ' Please verify and update your password.'
        ]; 

        // queue to mail job object and function 
        
        //$email = new SendEmailJob($sendto, $details);
        dispatch(new SendEmailJob($mail, $details));
        //$email->handle();

        //Mail::to($mail)->send(new testmail($details));
        return response()->json(['Message' => 'An OTP has been sent to '.$mail.' , Please verify and proceed further.']);
    }

}