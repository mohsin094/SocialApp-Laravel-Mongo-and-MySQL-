<?php

namespace App\Http\Controllers;
use App\Http\Requests\CommentRequest;
use MongoDB\Client as Connection;

class CommentController extends Controller
{
    //post function
    public function addComment(CommentRequest $request)
    {

        $validate = $request->validated();
        $token=$request->bearerToken();
            $userObj = new UserController();
            $data =  $userObj->decodeToken($token);

            $fileName = time().'_'.$validate['file']->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('comments', $fileName, 'public');
            $post_id = new \MongoDB\BSON\ObjectId($validate['post_id']);

            $comment =array(
                '_id'=>new \MongoDB\BSON\ObjectId(),
                'user_id'=>$data->id,
                'file'=>$filePath,
                'body'=>$validate['body'],
            );

            $db=(new Connection)->socialApp->posts;
            $result = $db->updateOne(["_id"=>$post_id],['$push'=>["comments"=>$comment]]);
        if (isset($result)) {
            return response()->success('Your comment is publish successfully!',200);

        }

    }
}
