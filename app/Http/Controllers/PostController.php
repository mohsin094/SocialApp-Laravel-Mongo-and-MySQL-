<?php

namespace App\Http\Controllers;
use App\Http\Requests\PostRequest;
use MongoDB\Client as connection;
class PostController extends Controller
{
    //post function
    public function publishPost(PostRequest $request)
    {
        $validate = $request->validated();
        $token=$request->bearerToken();
            $obj = new UserController();
            $data = $obj->decodeToken($token);

            $dbPost =(new connection)->socialApp->posts;
            $fileName = time().'_'.$validate['file']->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

            $result = $dbPost->insertOne([
                'user_id'=>$data->id,
                'caption' => $validate['caption'],
                'body' => $validate['body'],
                'file'=>'/storage/' . $filePath,
                'visibile'=>$validate['visibile'],
                ]);
        if ($result) {
            return response()->success('Your post is publish successfully!',200);
        }
}
}
