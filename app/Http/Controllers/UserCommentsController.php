<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserCommentValidation;
use App\Http\Requests\UserCommentUpdateValidation;
use App\Http\Requests\UserCommentDeleteValidation;

class UserCommentsController extends Controller
{

    // user comments
    function user_comments(UserCommentValidation $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;

        if(!empty($user_record))
        {
            $pid = $req->input('pid');
            $comment = $req->input('comment');
            
            if($req->file != null)
            {
                $file = $req->file('file')->store('comments');
            }
            else
            {
                $file = null;
            }

            // get user id from users_record
            $uid = $user_record->uid;

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

    
    // user updates comment
    function user_comments_update(UserCommentUpdateValidation $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;
        

        if(!empty($user_record))
        {
            $cid = $req->input('cid');
            $comment = $req->input('comment');
    
            if($req->file != null)
            {
                $file = $req->file('file')->store('comments');
            }
            else
            {
                $file = null;
            }

            // get user id from users_record
            $uid = $user_record->uid;

            DB::table('comments')->where(['cid' => $cid, 'user_id' => $uid])->update(['comments' => $comment, 'file' => $file]);

            return response(['Message' => 'Your Comment has been updated.']);
        }
        else
        {
            return response(['Message' => 'Something went wrong in while updating comment..!!!']);
        }
    }


    // user delete comment
    function user_comment_delete(UserCommentDeleteValidation $req)
    {
        $req->validated();

        // get all record of user from middleware where token is getting checked
        $user_record = $req->user_data;        

        if(!empty($user_record))
        {
            $cid = $req->input('cid');

            // get user id from users_record
            $uid = $user_record->uid;

            DB::table('comments')->where(['cid' => $cid, 'user_id' => $uid])->delete();

            return response(['Message' => 'Your Comment has been deleted.']);
        }
        else
        {
            return response(['Message' => 'Something went wrong in while deleted comment..!!!']);
        }     
    }
}
