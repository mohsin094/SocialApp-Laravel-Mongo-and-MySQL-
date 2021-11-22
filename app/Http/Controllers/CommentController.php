<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{

    //post function
    public function addComment(Request $request)
    {
        $token=$request->bearerToken();

        if (User::where("remember_token", $token)->exists()){
            $userObj = new UserController();
            $data =  $userObj->decodeToken($token);
           
            $validate =Validator::make($request->all(), [
                'post_id'=>'required|integer',
                 'body' => 'required|string|between:2,100',
                //'body' => 'string|mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
            ]);
            if ($validate->fails()) {
                return response()->json( $validate->errors()->toJson(),400);
            }

            $user =User::find($data->id);
            $post = Post::find($request->post_id);
            
            $comment = new Comment;
            $comment->body=$request->body;

        //go to comment model, check call users() function and put user key as a foreign key in comment table
           $comment->users()->associate($user);
           $comment->posts()->associate($post);
           
           $result = $comment->save();

        if ($result) {
            return response()->json(
                [
                    'Message'=>"Your comment is publish successfully"
                ],200
            );
        } else {
            return response()->json(
                [
                    'Error'=>"Error in publishing comment"
                ],400
            );
        }

        }

    }
}
