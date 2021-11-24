<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserCredentialsController;     
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/


// user signup
Route::post('/signup', [UserCredentialsController::class, 'signup']);

// user email verification
Route::get('/welcome_login/{email}/{verify_token}', [UserCredentialsController::class, 'welcome_to_login']);

// user forget password
Route::post('/forget_password', [UserCredentialsController::class, 'userForgetPassword']);

// user change password
Route::post('/change_password', [UserCredentialsController::class, 'userChangePassword']);

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
});
