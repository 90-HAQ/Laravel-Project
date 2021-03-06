<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use App\Mail\testmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\SignupValidation;
use App\Http\Requests\LoginValidation;
use App\Http\Requests\UserUpdateDetailsValidation;
use App\Http\Requests\UserForgetValidation;
use App\Http\Requests\UserChangePasswordValidation;
use App\Http\Resources\UserResource;
use App\Services\JWT_Service;
use App\Services\Email_Service;


class UserCredentialsController extends Controller
{ 
    // user signup 
    public function signup(SignupValidation $req)
    {
        try
        {
            $user = new User;
            $user->name = $req->input('name');
            $user->email = $req->input('email');
            $user->password = Hash::make($req->input('password')); // return hashed password
            $user->gender = $req->input('gender');
            $user->status = 0;
            $user->verify_token = rand(10, 5000);
    
            // parameters for mail sending function.
            $sendto = $user->email;
            $verify_token = $user->verify_token;
    
            // save data in db
            $result = $user->save();
    
            if($result)
            {
                $send_email_verify = new Email_Service();
                $result = $send_email_verify->sendmail($sendto, $verify_token);
                return response()->json($result,200);
            }
            else
            {
                return response()->json(['Message'=>'Something went wrong in Signup Api..!!!'], 400);
            }           
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // welcome api for user email verification and updation at backend
    public function welcome_to_login($email, $verify_token)
    {
        try
        {
            $data = DB::table('users')->where('email', $email)->where('verify_token', $verify_token)->get();
        
            $wordCount = count($data);
    
            if($wordCount > 0)
            {
                DB::table('users')->where('email', $email)->update(['email_verified_at'=> now()]);
                DB::table('users')->where('email', $email)->update(['updated_at'=> now()]);
    
                return response()->json(['Message'=>'Your Email has been Verified'], 200);
            }
            else
            {
                return response()->json(['Message' => 'Something went wrong in Welcome To Login Api..!!!'], 400);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user login
    public function login(LoginValidation $req)
    {
        try
        {
            $pas = 0;
            $status = 0;

            // get all record of user from verifiedAccount-middleware where email_verified_at is getting checked.
            $user_record = $req->user_data;
            $pas = $user_record->password; 
            $status = $user_record->status;  
    
            $user = new User;
            $user->email = $req->input('email');
            $user->password = $req->input('password');
    
            if(Hash::check($user->password, $pas))
            {
                if($status == 0)
                {
                    $jwt_connection = new JWT_Service();
    
                    $jwt = $jwt_connection->get_jwt();
                    // check if jwt is generating or not.
                    //echo $jwt;
    
                    DB::table('users')->where('email', $user->email)->update(['remember_token' => $jwt, 'status'=> '1']);
    
                    return response()->json(['Message' => 'Now you are logged In', 'access_token' => $jwt], 200);
                }
                else
                {
                    return response()->json(['Message' => 'You are Already Logged In..!!!'], 400);
                }
            }
            else
            {
                return response()->json(['Message' => 'Your email '.$user->email.' does not exists in our record.'], 404);
                //return response(['Message' => 'Your email '.$user->email.' is not verified. Please verify your email first.']);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user forgets password after signup and can't login, so reset password.
    function userForgetPassword(UserForgetValidation $req)
    {
        try
        {
            $user = new User;
            $mail = $user->email = $req->input('email');
    
            $data = DB::table('users')->where('email', $mail)->first();
            
            if(!empty($data))
            {
                // get data of email verified from user
                $verfiy =$data->email_verified_at;
    
                if(!empty($verfiy))
                {
                    $otp=rand(1000,9999);
                    DB::table('users')->where('email', $mail)->update(['verify_token'=> $otp]);
    
                    $send_email_verify = new Email_Service();
                    $result = $send_email_verify->sendMailForgetPassword($mail,$otp);
                    return response()->json(['Message'=> $result], 200);
                }
                else{
                    return response()->json(['Message'=>'User not Exists'], 404);
                }
            }
            else
            {
                return response()->json(['Message'=>'User not Exists'], 404);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // get otp-token and veirfy then update the user new password.
    function userChangePassword(UserChangePasswordValidation $req)
    {
        try
        {
            $user = new User;
            $mail = $user->email = $req->input('email');
            $token = $user->otp = $req->input('otp');
            $pass=Hash::make($req->input('password'));
    
            $data = DB::table('users')->where('email', $mail)->first();
            
            if(!empty($data))
            {
                $token1 =$data->verify_token;
    
                if($token1==$token)
                {
                    DB::table('users')->where('email', $mail)->update(['password'=> $pass]);
    
                    return response()->json(['Message'=>'Your Password has been updated so now you can login easily.. Thankyou..!!!!. '], 200);
                }
                else{
                    return response()->json(['Message'=>'Otp Does Not Match. '], 404);
                }
            }
            else{
                return response()->json(['Message'=>'Please Enter Valid Mail. '], 404); 
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user update details
    function user_update_details(UserUpdateDetailsValidation $req)
    {
        try
        {
            // get all record of user from middleware where token is getting checked.
            $user_record = $req->user_data;

            if(!empty($user_record))
            {
                // get token from middleware
                $token = $user_record->remember_token;

                // get user details to update user credentials
                $name = $req->input('name');
                $password = Hash::make($req->input('password')); // return hashed password

                DB::table('users')->where('remember_token', $token)->update(['name' => $name, 'password' => $password]);

                return response()->json(['Message' => 'User Credentials Updated'], 200);    
            }
            else
            {
                return response()->json(['Message' => 'This user does not exist...!!'], 404);
            } 
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user view all data and posts as well
    public function user_details_and_posts_details(Request $req)
    {
        try
        {
            // get all record of user from middleware where token is getting checked
            $user_record = $req->user_data;

            if(!empty($user_record))
            {
                // get user id from middleware
                $uid = $user_record->uid;

                $data = User::with(['AllUserPost','AllUserPostComments'])->where('uid', $uid)->get();

                //return response()->json(['Message' => $data]);

                // resourse will displa all data of user.
                return new UserResource($data);
            }
            else
            {
                return response()->json(['Message' => 'This user does not exist...!!'], 404);
            } 
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user logout
    public function user_logout(Request $req)
    {
        try
        {
            // get all record of user from middleware where token is getting checked
            $user_record = $req->user_data;

            if(!empty($user_record))
            {
                // get token id from middleware 
                $token = $user_record->remember_token;

                DB::table('users')->where(['remember_token' => $token])->update(['status'=> '0']);
                DB::table('users')->where(['remember_token' => $token])->update(['remember_token' => null]);

                return response()->json(['Message' => 'Logout Succeccfully..!!'],200);
            }
            else
            {
                return response()->json(['Message' => 'Token not found or expired..!!'], 404);
            } 
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }
}
