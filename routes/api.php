<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserCredentialsController;     
use App\Http\Controllers\UserMakeFriendsController;
use App\Http\Controllers\UserPostsController;
use App\Http\Controllers\UserUpdateController;
use App\Http\Controllers\UserCommentsController;

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
Route::post('/signup', [UserCredentialsController::class, 'signup']);


// user email verification
Route::get('/welcome_login/{email}/{verify_token}', [UserCredentialsController::class, 'welcome_to_login']);


// user login
Route::post('/login', [UserCredentialsController::class, 'login']);


// token authentication
Route::group(['middleware' => "tokenAuth"], function()
{
    // user logout
    Route::post('/logout', [UserCredentialsController::class, 'user_logout']);

    
    // User details and post details
    Route::post('/user_post_details', [UserCredentialsController::class, 'user_details_and_posts_details']);
    

    // UserUpdateController details
    Route::post('/user_update', [UserCredentialsController::class, 'user_update_details']);


    // user add friends
    Route::post('/add_friend', [UserMakeFriendsController::class, 'user_add_friends']);


    // user add post
    Route::post('/add_post', [UserPostsController::class, 'create_post']);


    // user view all post
    Route::post('/view_post', [UserPostsController::class, 'view_post']);


    // user update post
    Route::post('/update_post', [UserPostsController::class, 'update_post']);


    // user delete post
    Route::post('/delete_post', [UserPostsController::class, 'delete_post']);


    // user comments
    Route::post('/user_comments', [UserCommentsController::class, 'user_comments']);


    // user comments updation
    Route::post('/user_comments_update', [UserCommentsController::class, 'user_comments_update']);
});
