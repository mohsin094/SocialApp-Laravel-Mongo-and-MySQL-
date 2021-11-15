<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
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

Route::post("register",[UserController::class,'registration']);
Route::post("login",[UserController::class,'login']);
Route::get("verifyemail/{email}",[UserController::class,'verify']);
Route::post("logout",[UserController::class,'logout']);


Route::post("post",[PostController::class,'post']);
Route::post("comment",[CommentController::class,'addComment']);
Route::post("addFriend",[FriendController::class,'addFriend']);