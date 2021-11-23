<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\FriendController;
use Illuminate\Support\Facades\Route;

Route::post("registration",[UserController::class,'registration']);
Route::post("login",[UserController::class,'login']);
Route::post("logout",[UserController::class,'logout']);
Route::get("verifyemail/{email}",[UserController::class,'verify']);
Route::post("addFriend",[FriendController::class,'addFriend'])->middleware('userAuthentication');