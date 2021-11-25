<?php

namespace App\Http\Controllers;
use App\Http\Requests\CommentRequest;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{

    //post function
    public function addComment(CommentRequest $request)
    {
        $token=$request->bearerToken();
        $validate = $request->validated();

        if (User::where("remember_token", $token)->exists()){
            $userObj = new UserController();
            $data =  $userObj->decodeToken($token);

            $user =User::find($data->id);
            $post = Post::find($validate['post_id']);

            $comment = new Comment;
            $comment->body=$validate['body'];

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
