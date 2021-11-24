<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserCreatePostValidation;
use App\Http\Requests\UserUpdatePostValidation;
use App\Http\Requests\UserDeletePostValidation;

class UserPostsController extends Controller
{
    // user create post
    function create_post(UserCreatePostValidation $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;
    
        if(!empty($user_record))
        {
            $file = $req->file('file')->store('post');
            $access =  $req->input('access');    

            // get user id from users_record
            $id = $user_record->uid;

            $val=array('user_id'=>$id, 'file'=>$file, 'access'=>$access);
            DB::table('posts')->insert($val);
            return response(['Message'=>'Post Successfull.']);
        }
        else
        {
            return response(['Message'=>'Please login First / No Record Found']);
        }
    }


    // user view all his own posts
    function view_post(Request $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;

        if(!empty($user_record))
        {
            // gets specfic data against uid
            $uid = $user_record->uid;

            $data = DB::table('posts')->where('user_id', $uid)->get();

            // gets all posts from table
            //$data = DB::table('posts')->get();

            return response(['Message'=> $data]);
        }
        else
        {
            return response(['Message'=>'Please login First / Token Expired.']);
        }
    }


    // user updates post
    function update_post(UserUpdatePostValidation $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;

        if(!empty($user_record))
        {
            $pid = $req->input('pid');
            $file = $req->input('file');
            //$file = $req->file('file')->store('post');
            $access = $req->input('access');

            // gets specfic data against uid
            $uid = $user_record->uid;

            DB::table('posts')->where(['pid' => $pid, 'user_id' => $uid])->update(['file'=> $file,'access'=> $access,]);

            return response(['Message'=>'Post Updated']);
        }
        else
        {
            return response(['Message'=>'Please login First / Token Expired.']);
        }
    }


    // user delete post
    function delete_post(UserDeletePostValidation $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;

        if(!empty($user_record))
        {
            // get pid from user request
            $pid = $req->input('pid');

            // get user id from middleware 
            $uid = $user_record->uid;

            // delete comments from comments table first
            DB::table('comments')->where('post_id', $pid)->delete();            

            // delete posts from posts table 
            $post = DB::table('posts')->where(['pid' => $pid, 'user_id' => $uid])->delete();

            if($post == 1)
            {
                return response(['Message'=>'Post and Comments on that post deleted successfully.']);   
            }
            else
            {
                $check2 = "You are not allowed to delete this post, because this post belongs to someone else. / or this post does not exists.";
                return response(['Message' => $check2]);                                 
            }                
        }
        else
        {
            return response(['Message'=>'Post Id does not exist.']);
        }
    }
}
