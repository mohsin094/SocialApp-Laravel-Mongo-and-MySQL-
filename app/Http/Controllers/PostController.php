<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
class PostController extends Controller
{

    //post function
    public function post(Request $request)
    {
        $token=$request->bearerToken();

        if (User::where("remember_token", $token)->exists()){
            $obj = new UserController();
            $data = $obj->decodeToken($token);
            

            $validate =Validator::make($request->all(), [
                'caption' => 'required|string|between:2,100',
                'body'=> 'required|string|max:1000',
                'file' => 'mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
                'visibile'=>'boolean',
            ]);
            if ($validate->fails()) {
                return response()->json( $validate->errors()->toJson(),400);
            }

            //check the user eixt in usre table
            $user = User::find($data->id);
            
            $posts = new Post;
            $posts->caption=$request->caption;
            $posts->body=$request->body;
            $posts->visibile=$request->visibile;
                    $fileName = time().'_'.$request->file->getClientOriginalName();
                    $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $posts->file = '/storage/' . $filePath;

        $result = $user->posts()->save($posts);
        if ($result) {
            return response()->json(
                [
                    'Message'=>"Your post is publish successfully"
                ],200
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
