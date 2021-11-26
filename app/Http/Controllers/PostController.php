<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\User;
use App\Models\Post;

class PostController extends Controller
{

    //post function
    public function post(PostRequest $request)
    {
        $validate = $request->validated();
        $data = $request->data;
        //check the user eixt in usre table
        $user = User::find($data->id);

        $posts = new Post;
        $posts->caption = $validate['caption'];
        $posts->body = $validate['body'];
        $posts->visibile = $validate['visibile'];
        $fileName = time() . '_' . $validate['file']->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
        $posts->file = '/storage/' . $filePath;

        $result = $user->posts()->save($posts);
        if ($result) {
            return response()->success('Your post is publish successfully', 200);
        } else {
            return response()->error('Error in publishing post', 400);
        }
    }
}
