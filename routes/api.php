<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user_signup_login;     
use App\Http\Controllers\user_logout;
use App\Http\Controllers\user_make_friends;
use App\Http\Controllers\user_posts;
use App\Http\Controllers\user_update;
use App\Http\Controllers\user_comments;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// user signup
Route::post('/signup', [user_signup_login::class, 'signup']);


// user email verification
Route::get('/welcome_login/{email}/{verify_token}', [user_signup_login::class, 'welcome_to_login']);


// user login
Route::post('/login', [user_signup_login::class, 'login']);


// user_update_details
Route::post('/user_update', [user_update::class, 'user_update_details']);


// user add friends
Route::post('/add_friend', [user_make_friends::class, 'user_add_friends']);


// user add post
Route::post('/add_post', [user_posts::class, 'create_post']);


// user view all post
Route::post('/view_post', [user_posts::class, 'view_post']);


// user update post
Route::post('/update_post', [user_posts::class, 'update_post']);


// user delete post
Route::post('/delete_post', [user_posts::class, 'delete_post']);


// user comments
Route::post('/user_comments', [user_comments::class, 'user_comments']);


// user comments updation
Route::post('/user_comments_update', [user_comments::class, 'user_comments_update']);


// user logout
Route::post('/logout', [user_logout::class, 'user_logout']);
