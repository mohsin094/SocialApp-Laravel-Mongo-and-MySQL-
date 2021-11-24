<?php

namespace App\Http\Controllers;
use App\Http\Requests\PostRequest;
use App\Services\DbConnection;

class PostController extends Controller
{
    public function publishPost(PostRequest $request)
    {
        try{
            $validate = $request->validated();
            $token=$request->bearerToken();
                $userData = (new UserController())->decodeToken($token);
                $dbPost =(new DbConnection('posts'))->getConnection();

                $fileName = time().'_'.$validate['file']->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

                $result = $dbPost->insertOne([
                    'user_id'=>$userData->id,
                    'caption' => $validate['caption'],
                    'body' => $validate['body'],
                    'file'=>'/storage/' . $filePath,
                    'visibile'=>$validate['visibile'],
                    ]);
            if ($result) {
                return response()->success('Your post is publish successfully!',200);
            }
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
}
}
