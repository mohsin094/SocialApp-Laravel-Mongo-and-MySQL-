<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ViewPost extends Controller
{
    //post function
    public function post(Request $request)
    {
        $token=$request->bearerToken();

        if (c){
            $obj = new UserController();
            $data = $obj->decodeToken($token);
            $posts = new Post;

            $validate =Validator::make($request->all(), [
                'post_id' => 'required|integer|between:2,100',
            ]);
            if ($validate->fails()) {
                return response()->json( $validate->errors()->toJson(),400);
            }

            $result=User::where("remember_token", $token)->exists()

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
