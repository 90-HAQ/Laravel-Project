<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\UserCreatePostValidation;
use App\Http\Requests\UserUpdatePostValidation;
use App\Http\Requests\UserDeletePostValidation;
use App\Http\Resources\PostResource;

class UserPostsController extends Controller
{
    // user create post
    function create_post(UserCreatePostValidation $req)
    {
        try
        {
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

                return response()->json(['Message'=>'Post Successfull.'], 200);
            }
            else
            {
                return response()->json(['Message'=>'Please login First / No Record Found'], 404);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user view all his own posts
    function view_post(Request $req)
    {
        try
        {
            // get all record of user from middleware where token is getting checked
            $user_record = $req->user_data;

            if(!empty($user_record))
            {
                // gets specfic data against uid
                $uid = $user_record->uid;

                $data = DB::table('posts')->where('user_id', $uid)->get();

                // gets all posts from table
                //$data = DB::table('posts')->get();

                // resourse will displa all data of users post.
                return new PostResource($data);
            }
            else
            {
                return response(['Message'=>'Please login First / Token Expired.'], 404);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user updates post
    function update_post(UserUpdatePostValidation $req)
    {
        try
        {
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

                return response()->json(['Message'=>'Post Updated'], 200);
            }
            else
            {
                return response()->json(['Message'=>'Please login First / Token Expired.'], 404);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }


    // user delete post
    function delete_post(UserDeletePostValidation $req)
    {
        try
        {
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
                    return response()->json(['Message'=>'Post and Comments on that post deleted successfully.'], 200);   
                }
                else
                {
                    $check2 = "You are not allowed to delete this post, because this post belongs to someone else. / or this post does not exists.";
                    return response()->json(['Message' => $check2], 403);                                 
                }                
            }
            else
            {
                return response()->json(['Message'=>'Post Id does not exist.'], 404);
            }
        }
        catch(\Exception $show_error)
        {
            return response()->json(['Error' => $show_error->getMessage()], 500);
        }
    }
    // try
    // {

    // }
    // catch(\Exception $show_error)
    // {
    //     return response()->json(['Error' => $show_error->getMessage()], 500);
    // }
}
