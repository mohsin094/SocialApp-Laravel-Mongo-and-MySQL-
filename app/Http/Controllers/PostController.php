<?php

namespace App\Http\Controllers;
use App\Http\Requests\PostRequest;
use App\Models\Friends;
use App\Services\DbConnection;
use Illuminate\Http\Request;
class PostController extends Controller
{
    public function publishPost(PostRequest $request)
    {
        try{
            $validate = $request->validated();
                $userData = $request->data;
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


    public function viewPosts(Request $request)
    {
        try{
                //data of token
                $data = $request->data;
                $dbUser =(new DbConnection('users'))->getConnection();
                $dbPost =(new DbConnection('posts'))->getConnection();
                $user_id = new \MongoDB\BSON\ObjectId($data->id);

                //dd($user_id);
                 $usercursor = $dbUser->findOne(['_id'=>$user_id]);
                 foreach ($usercursor->friends as $friend) {
                    $friend_id =(string) $friend->friend_id;
                    $post_data = $dbPost->find(['user_id'=>$friend_id]);
                    foreach ($post_data as $d) {
                        $data=array(
                            "Post data"=>$d
                        );
                        return response()->success($data,200);
                    }
                 }
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }
}
