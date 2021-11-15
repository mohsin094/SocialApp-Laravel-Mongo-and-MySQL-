<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
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
            $comment = new Comment;

            $validate =Validator::make($request->all(), [
                'post_id'=>'required|integer',
                 'body' => 'required|string|between:2,100',
                //'body' => 'string|mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
            ]);
            if ($validate->fails()) {
                return response()->json( $validate->errors()->toJson(),400);
            }

            $comment->post_id=$request->post_id;
            $comment->body=$request->body;
            $comment->user_id=$data->id;

        $result = $comment->save();
        if ($result) {
            return response()->json(
                [
                    'Message'=>"Your comment is publish successfully"
                ],400
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
