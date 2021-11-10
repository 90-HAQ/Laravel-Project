<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class user_logout extends Controller
{
    function user_logout(Request $req)
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
