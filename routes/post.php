<?php
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::post("post",[PostController::class,'publishPost'])->middleware('userAuthentication');
Route::post("comment",[CommentController::class,'addComment'])->middleware('userAuthentication');