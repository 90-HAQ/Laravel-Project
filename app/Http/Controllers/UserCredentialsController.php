<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Mail\testmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class UserCredentialsController extends Controller
{ 
    // mail sending function
    public function sendmail($sendto, $verify_token)
    {
        $details = [
            'title' =>  'Signup Verification.',
            'body'  =>  'Please Verify your Account. Please Click on this link to verify http://127.0.0.1:8000/api/welcome_login'.'/'.$sendto.'/'.$verify_token
        ];

        Mail::to($sendto)->send(new testmail($details));
        return response(['Message' => 'Email has been sent for Verification, Please verify your Account.']);
    }



    // signup api
    public function signup(Request $req)
    {

        $validation = Validator::make($req->all(),[
            'name'      =>  'required|string',
            'email'     =>  'required|email|unique:users',
            'password'  =>  'required|min:8|string',
            'gender'    =>  'required|Alpha',   
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
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
                $result = $this->sendmail($sendto, $verify_token);
                return response($result,200);
            }
            else
            {
                return response(['Message'=>'Something went wrong in Signup Api..!!!']);
            }
        }        
    }



    // welcome api for user email verification and updation
    public function welcome_to_login($email, $verify_token)
    {
        //return "Hello $email welcome to login page and verify_token is :  $verify_token";

        $data = DB::table('users')->where('email', $email)->where('verify_token', $verify_token)->get();
        
        $wordCount = count($data);


        if($wordCount > 0)
        {
            DB::table('users')->where('email', $email)->update(['email_verified_at'=> now()]);
            DB::table('users')->where('email', $email)->update(['updated_at'=> now()]);
            return response(['Message'=>'Your Email has been Verified']);
        }
        else
        {
            return response(['Message' => 'Something went wrong in Welcome To Login Api..!!!']);
        }
    }


    // login api
    public function login(Request $req)
    {
        $pas = 0;
        $status = 0;
        $email_verified = 0;

        $validation = Validator::make($req->all(),
        [
            'email'     =>  'required|email',
            'password'  =>  'required|min:8|string',
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
        {

            $user = new User;
            $user->email = $req->input('email');
            $user->password = $req->input('password');
            

            $data = DB::table('users')->where('email', $user->email)->get();

            foreach($data as $key )
            {
                //to get each columns value
                //$value->name
                $pas = $key->password; 
                $status = $key->status;  
                $email_verified = $key->email_verified_at; 
            }

            // check if data exists in variables or not
            //dd($email_verified);
            //dd($pas);

            if(!empty($email_verified) && Hash::check($user->password, $pas))
            {
                if($status == 0)
                {
                    // jwt token generate

                    $key = "90HAQ";
                    $payload = array(
                        "iss" => "localhost",
                        "aud" => "users",
                        "iat" => time(),
                        "nbf" => 1357000000
                    );
                    $jwt = JWT::encode($payload, $key, 'HS256');

                    //echo $jwt;

                    DB::table('users')->where('email', $user->email)->update(['remember_token' => $jwt]);

                    DB::table('users')->where('email', $user->email)->update(['status'=> '1']);

                    return response(['Message' => 'Now you are logged In', 'access_token' => $jwt]);
                }
                else
                {
                    return response(['Message' => 'You are Already Logged In..!!!']);
                }
            }
            else
            {
                return response(['Message' => 'Your email '.$user->email.' does not exists in our record '.'because your email is not verified. Please verify your email first.']);
                //return response(['Message' => 'Your email '.$user->email.' is not verified. Please verify your email first.']);
            }
        }
    }


    // user update details
    function user_update_details(Request $req)
    {
        $validation = Validator::make($req->all(),
        [
            'token'      =>  'required',
            'name'      =>  'required|string',
            'password'  =>  'required|min:8|string',
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
        {
            $token = $req->token;

            if(!empty($token))
            {
                $name = $req->name;
                $password = Hash::make($req->password); // return hashed password
    
                DB::table('users')->where('remember_token', $token)->update(['name' => $name, 'password' => $password]);
                return response(['Message' => 'User Credentials Updated']);    
            }
            else
            {
                return response(['Message' => 'User not found / Token Expired.']);
            }
        } 

    }


    // user view all data and posts as well
    public function user_details_and_posts_details(Request $req)
    {
        $token = $req->token;

        $data = DB::table('users')->where(['remember_token' => $token])->get();

        $uid = $data[0]->uid;

        $check = count($data);

        if($check > 0)
        {
            $data = User::with('AllUserPost')->where('uid', $uid)->get();
            return response(['Message' => $data]);
        }
        else
        {
            return response(['Message' => 'Token not found or expired..!!']);
        } 

    }


    // user logout
    public function user_logout(Request $req)
    {
        $token = $req->token;

        $data = DB::table('users')->where(['remember_token' => $token])->get();

        $check = count($data);

        if($check > 0)
        {
            DB::table('users')->where(['remember_token' => $token])->update(['status'=> '0']);
            DB::table('users')->where(['remember_token' => $token])->update(['remember_token' => null]);
         
            return response(['Message' => 'Logout Succeccfully..!!']);
        }
        else
        {
            return response(['Message' => 'Token not found or expired..!!']);
        } 
    }
}
