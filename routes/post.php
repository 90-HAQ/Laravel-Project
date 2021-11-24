<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserPostsController;

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


// token authentication
Route::group(['middleware' => "tokenAuth"], function()
{
    // user add post
    Route::post('/add_post', [UserPostsController::class, 'create_post']);


    // user view all post
    Route::post('/view_post', [UserPostsController::class, 'view_post']);


    // user update post
    Route::post('/update_post', [UserPostsController::class, 'update_post']);


    // user delete post
    Route::post('/delete_post', [UserPostsController::class, 'delete_post']);
});
