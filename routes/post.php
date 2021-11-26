<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::post("post",[PostController::class,'post'])->middleware('UerAuthentication');
Route::post("comment",[CommentController::class,'addComment'])->middleware('UerAuthentication');
