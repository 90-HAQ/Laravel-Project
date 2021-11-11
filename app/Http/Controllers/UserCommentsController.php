<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserCommentsController extends Controller
{
    function user_comments(Request $req)
    {

        $validation = Validator::make($req->all(),
        [
            'token'      =>  'required',
            'pid'  =>  'required',
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
                
                $pid = $req->pid;
                $comment = $req->comment;
                if($req->file != null)
                {
                    $file = $req->file('file')->store('comments');
                }
                else
                {
                    $file = null;
                }
                
        
                $data = DB::table('users')->where('remember_token', $token)->get();
        
                $wordcount = count($data);
    
                if($wordcount > 0)
                {
                    // get user id from 
                    $uid = $data[0]->uid;
    
                    // add data into friends table    
                    $values = array('user_id' => $uid, 'post_id' => $pid, 'comments' => $comment, 'file' => $file);
                    DB::table('comments')->insert($values);
        
                    return response(['Message' => 'Comment Uploaded on Post...!!!']);
                }
                else
                {
                    return response(['Message' => 'User does not exist in database.']);
                }
            }
            else
            {
                return response(['Message' => 'Login Account Again / Token expired.']);
            }    
        }
    }

    function user_comments_update(Request $req)
    {
        $validation = Validator::make($req->all(),
        [
            'token'      =>  'required',
            'pid'  =>  'required',
            'cid'  =>  'required',
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
                
                $cid = $req->cid;
                $pid = $req->pid;
                $comment = $req->comment;
                if($req->file != null)
                {
                    $file = $req->file('file')->store('comments');
                }
                else
                {
                    $file = null;
                }
                
                
                $data = DB::table('users')->where('remember_token', $token)->get();
        
                $wordcount = count($data);
        
                if($wordcount > 0)
                {
                    // get user id from 
                    $uid = $data[0]->uid;

                    DB::table('comments')->where(['cid' => $cid, 'post_id' => $pid,'user_id' => $uid])->update(['comments' => $comment, 'file' => $file]);

                    return response(['Message' => 'Your Comment has been updated.']);
                }
                else
                {
                    return response(['Message' => 'Something went wrong in while updating comment..!!!']);
                }
            }
        }        
    }
}
