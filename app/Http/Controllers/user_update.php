<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class user_update extends Controller
{
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
}
