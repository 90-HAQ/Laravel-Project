<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/



// token authentication
Route::group(['middleware' => "tokenAuth"], function()
{
    // user comments
    Route::post('/user_comments', [UserCommentsController::class, 'user_comments']);

    // user comments updation
    Route::post('/user_comments_update', [UserCommentsController::class, 'user_comments_update']);

    // user delete comment
    Route::post('/user_delete_comment', [UserCommentsController::class, 'user_comment_delete']);
});
