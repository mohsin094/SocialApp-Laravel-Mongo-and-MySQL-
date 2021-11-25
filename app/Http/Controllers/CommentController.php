<?php

namespace App\Http\Controllers;
use App\Http\Requests\CommentRequest;
use App\Services\DbConnection;

class CommentController extends Controller
{
    //post function
    public function addComment(CommentRequest $request)
    {
        try{
            $validate = $request->validated();
                $data = $request->data;

                $fileName = time().'_'.$validate['file']->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('comments', $fileName, 'public');
                $post_id = new \MongoDB\BSON\ObjectId($validate['post_id']);

                $comment =array(
                    '_id'=>new \MongoDB\BSON\ObjectId(),
                    'user_id'=>$data->id,
                    'file'=>$filePath,
                    'body'=>$validate['body'],
                );
                $dbPosts =(new DbConnection('posts'))->getConnection();
                $result = $dbPosts->updateOne(["_id"=>$post_id],['$push'=>["comments"=>$comment]]);
            if (isset($result)) {
                return response()->success('Your comment is publish successfully!',200);

            }
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }
}
