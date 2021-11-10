<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class user_posts extends Controller
{
    // create post
    function create_post(Request $req)
    {

        $validation = Validator::make($req->all(),[
            'token'      =>  'required',
            'file'     =>  'required',
            'access'  =>  'required',
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
        {
            $user = new Post;
        
            $token = $user->token = $req->input('token');
            //$file = $user->file = $req->input('file');
            $file = $req->file('file')->store('post');
            $access = $user->access = $req->input('access');
    
            $data = DB::table('users')->where('remember_token', $token)->get();
            $check=count($data);
            
            if(!empty($token))
            {
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
            else
            {
                return response(['Message'=>'Please login First / Token Expired.']);
            }
        }
    }


    // view all posts
    function view_post(Request $req)
    {

        $validation = Validator::make($req->all(),[
            'token'      =>  'required',
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
        {
            $user = new Post;

            $token = $user->token = $req->input('token');
    
            $data = DB::table('users')->where('remember_token', $token)->get();
            $check=count($data);
    
            if(!empty($token))
            {
                if($check > 0)
                {
                    $uid = $data[0]->uid;
                    $data = DB::table('posts')->where('user_id', $uid)->get();
    
                    return response(['Message'=> $data]);
                }
                else
                {
                    return response(['Message'=>'Please login First / Token Expired.']);
                }
            }            
        }
    }

    // update post
    function update_post(Request $req)
    {
        $validation = Validator::make($req->all(),[
            'token'      =>  'required',
            'pid'      =>  'required',
            'file'     =>  'required',
            'access'  =>  'required',
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
        {
            $user = new Post;

            $token = $user->token = $req->input('token');
            $pid = $user->pid = $req->input('pid');
            $file = $user->file = $req->input('file');
            $access = $user->access = $req->input('access');
    
            $data = DB::table('users')->where('remember_token', $token)->get();
            $check=count($data);
    
            if(!empty($token))
            {
                if($check > 0)
                {
                    DB::table('posts')->where('pid', $pid)->update(['file'=> $file,'access'=> $access,]);
    
                    return response(['Message'=>'Post Updated']);
                }
                else
                {
                    return response(['Message'=>'Please login First / Token Expired.']);
                }
            }            
        }        
    }

    // delete post
    function delete_post(Request $req)
    {
        $validation = Validator::make($req->all(),[
            'token'      =>  'required',
        ]);

        if($validation->fails())
        {
            return response()->json($validation->errors()->toJson(),400);
        }
        else
        {
            $user = new Post;

            $token = $user->token = $req->input('token');
            $pid = $user->pid = $req->input('pid');
    
            $data = DB::table('users')->where('remember_token', $token)->get();
            $check=count($data);
    
            if(!empty($token))
            {
                if($check > 0)
                {
                    DB::table('posts')->where('pid', $pid)->delete();
    
                    return response(['Message'=>'Post Deleted']);
                }
                else
                {
                    return response(['Message'=>'Please login First / Token Expired']);
                }
            }      
        }
    }
}
