<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserMakeFriendsController;


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
    // user add friends
    Route::post('/add_friend', [UserMakeFriendsController::class, 'user_add_friends'])->middleware('validFriend');
});
