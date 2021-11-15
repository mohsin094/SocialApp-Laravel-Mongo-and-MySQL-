<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{

    //post function
    public function comment(Request $request)
    {
        $token=$request->bearerToken();

        if (User::where("remember_token", $token)->exists()){
            $data = $this->decodeToken($token);
            $posts = new Post;

            $validate =Validator::make($request->all(), [
                'caption' => 'required|string|between:2,100',
                'body'=> 'string|max:1000',
                'file' => 'mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
                'visibile'=>'boolean',
            ]);
            if ($validate->fails()) {
                return response()->json( $validate->errors()->toJson(),400);
            }


            $posts->user_id=$data->id;
            $posts->caption=$request->caption;
            $posts->body=$request->body;
            $posts->visibile=$request->visibile;
                    $fileName = time().'_'.$request->file->getClientOriginalName();
                    $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $posts->file = '/storage/' . $filePath;

        $result = $posts->save();
        if ($result) {
            return response()->json(
                [
                    'Message'=>"Your post is publish successfully"
                ],400
            );
        } else {
            return response()->json(
                [
                    'Error'=>"Error in publishing post"
                ],400
            );
        }

        }

    }
}
