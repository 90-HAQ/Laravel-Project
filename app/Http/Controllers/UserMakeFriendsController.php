<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserAddFriendValidation;

class UserMakeFriendsController extends Controller
{
    function user_add_friends(UserAddFriendValidation $req)
    {

        $req->validated();
        $user = new Friend;
        $token = $user->token = $req->input('token');
        $email = $user->email = $req->input('email');
        
        // get all data of uers-1
        $user1 = DB::table('users')->where(['remember_token' => $token])->get();

        // get all data of uers-2
        $user2 = DB::table('users')->where(['email' => $email])->get();

        // get count of all fetch records
        $wordcount1 = count($user1);
        $wordcount2 = count($user2);

        // to check if user-2 is email-verified or not
        $user2_verify = $user2[0]->email_verified_at;

        // get id of user-1
        $uid1 = $user1[0]->uid;
        $uid2 = $user1[0]->uid;

        // get id of user-2
        $uid2 = $user2[0]->uid;
        // get name of user-2
        $name2 = $user2[0]->name;

        // get all data of uers-3 from friends table
        $user3 = DB::table('friends')->where(['user_id1' => $uid1, 'user_id2' => $uid2])->get();

        // get count of all fetch records
        $wordcount3 = count($user3);

        // this if is for to check num of rows from user3 variable
        if($wordcount3 == 0)
        {
            // to check if friend user is email-verified or not
            if($wordcount1 > 0 && $wordcount2 > 0)
            {
                // this if is for to check num of rows from user1 variable  
                // this if is for to check num of rows from user2 variable  
                if(!empty($user2_verify))
                {                    
                    // user cannot add himself as friend.
                    if($uid1 != $uid2)
                    {
                        // add data into friends table    
                        $values = array('user_id1' => $uid1, 'user_id2' => $uid2);
                        DB::table('friends')->insert($values);
                        return response(['Message' => 'Congrats '.$name2.' is your friend now...!!!!']);
                    }
                    else
                    {
                        return response(['Message' => 'You cannot add yourself as a friend.']);   
                    }                            
                }       
                else
                {
                    return response(['Message' => 'Friend not Found / Friend is not verified']);                           
                } 
            }
            else
            {
                return response(['Message' => 'Friend not Found / Something went wrong with friend.']);
            }
        }
        else
        {
            return response(['Message' => 'Alread your Friend. No need to add friend again.']);
        }
    }
}
