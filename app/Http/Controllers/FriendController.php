<?php
namespace App\Http\Controllers;
use App\Http\Requests\FriendRequest;
use App\Services\DbConnection;

class FriendController extends Controller
{
     //post function
     public function addFriend(FriendRequest $request)
     {
        try{
            $validate =$request->validated();
            $data = $request->data;

                if($data->id==$validate['friend_id']){
                        return response()->error('User does not exist!',400);
                }

               $dbUsers =(new DbConnection('users'))->getConnection();
               $user_id = new \MongoDB\BSON\ObjectId($data->id);
               $friend_id = new \MongoDB\BSON\ObjectId($validate['friend_id']);

               $exist = $dbUsers->findOne(['_id'=>$friend_id]);
               if (isset($exist)){
                    $check = $dbUsers->findOne(["_id" => $user_id,'friends.friend_id'=>$friend_id]);
                    if(isset($check )){
                        return response()->error('You are already friends!',400);
                    }

                        $friend=array(
                            '_id'=>new \MongoDB\BSON\ObjectId(),
                            'friend_id'=>$friend_id,
                        );
                    $result = $dbUsers->updateOne(['_id'=>$user_id],['$push'=>['friends'=>$friend]]);
                    if ($result) {
                        return response()->success('Friend added successfully!',200);
                    } else {
                        return response()->error('Database error!!',400);
                    }
                }
                else{
                    return response()->error('User does not exist!',400);
                }
            }catch(\Exception $e){
                return response()->error($e->getMessage(),400);
            }
     }
}
