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
        $user = new Post;
        
            $token = $user->token = $req->input('token');
            $file = $req->file('file')->store('post');
            $access = $user->access = $req->input('access');
    
            $data = DB::table('users')->where('remember_token', $token)->get();
            $check=count($data);
            
            if($check > 0)
            {
                $id = $data[0]->uid;
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
        $user = new Post;
        $token = $user->token = $req->input('token');
        $data = DB::table('users')->where('remember_token', $token)->get();
        $check=count($data);

        if($check > 0)
        {
            // gets specfic data against uid
            $uid = $data[0]->uid;
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
        $user = new Post;
        $token = $user->token = $req->input('token');
        $pid = $user->pid = $req->input('pid');
        $file = $user->file = $req->input('file');
        $access = $user->access = $req->input('access');

        $data = DB::table('users')->where('remember_token', $token)->get();
        $uid = $data[0]->uid;
        $check=count($data);


            if($check > 0)
            {
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
        $user = new Post;

        $token = $user->token = $req->input('token');
        $pid = $user->pid = $req->input('pid');

        $data1 = DB::table('users')->where('remember_token', $token)->get();
        $uid = $data1[0]->uid;
        $top = count($data1);

        if($top > 0)
        {
            DB::table('comments')->where('post_id', $pid)->delete();            

            $post = DB::table('posts')->where(['pid' => $pid, 'user_id' => $uid])->delete();

            if($post == 1)
            {

                return response(['Message'=>'Post and Comments on that post deleted successfully.']);   
            }
            else
            {
                $check2 = "You are not allowed to delete this post, because this post belongs to someone else.";
                return response(['Message' => $check2]);                                 
            }                
        }
        else
        {
            return response(['Message'=>'Post Id does not exist.']);
        }
    }
}
