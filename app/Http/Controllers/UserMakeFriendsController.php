<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserAddFriendValidation;

class UserMakeFriendsController extends Controller
{
    function user_add_friends(UserAddFriendValidation $req)
    {
        try
        {   
            $uid1 = $req->friend_data['uid1'];
            $uid2 = $req->friend_data['uid2'];
            
            // add data into friends table    
            $values = array('user_id1' => $uid1, 'user_id2' => $uid2);
            DB::table('friends')->insert($values);

            return response()->json(['Message' => 'Congrats Friend Added...!!!!'], 200);
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }
}
