<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class validFriend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $req, Closure $next)
    {
        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;

        // get email to add friend from user request
        $email = $req->input('email');

        // get all data of uers-2
        $user2 = DB::table('users')->where(['email' => $email])->first();

        // to check if user-2 is email-verified or not
        $user2_verify = $user2->email_verified_at;

        // get id of user-1
        $uid1 = $user_record->uid;

        // get id of user-2
        $uid2 = $user2->uid;

        // get name of user-2
        $name2 = $user2->name;

        // get all data of uers-3 from friends table
        $user3 = DB::table('friends')->where(['user_id1' => $uid1, 'user_id2' => $uid2])->first();

        // this if is for to check num of rows from user3 variable
        if(empty($user3))
        {
            // to check if friend user is email-verified or not
            if(!empty($user_record) && !empty($user2))
            {
                // this if is for to check num of rows from user1 variable  
                // this if is for to check num of rows from user2 variable  
                if(!empty($user2_verify))
                {                    
                    // user cannot add himself as friend.
                    if($uid1 != $uid2)
                    {
                        // // add data into friends table    
                        // $values = array('user_id1' => $uid1, 'user_id2' => $uid2);
                        // DB::table('friends')->insert($values);

                        $insert = ['uid1' => $uid1, 'uid2' => $uid2];
                        return $next($req->merge(['friend_data' => $insert]));
                    }
                    else
                    {
                        return response()->json(['Message' => 'You cannot add yourself as a friend.']);   
                    }                            
                }       
                else
                {
                    return response(['Message' => 'Friend not Found / Friend is not verified']);                           
                } 
            }
            else
            {
                return response()->json(['Message' => 'Friend not Found / Something went wrong with friend.']);
            }
        }
        else
        {
            return response()->json(['Message' => 'Alread your Friend. No need to add friend again.']);
        }
    
    }
}
