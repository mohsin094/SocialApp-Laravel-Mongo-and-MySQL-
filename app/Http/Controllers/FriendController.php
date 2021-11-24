<?php
namespace App\Http\Controllers;
use App\Http\Requests\FriendRequest;
use MongoDB\Client as Connection;

class FriendController extends Controller
{
     //post function
     public function addFriend(FriendRequest $request)
     {
         $validate =$request->validated();

         $token=$request->bearerToken();
                $userObj = new UserController();
                $data =  $userObj->decodeToken($token);

               if($data->id==$validate['friend_id']){
                    return response()->error('User does not exist!',400);
               }

               $db=(new Connection)->socialApp->users;
               $user_id = new \MongoDB\BSON\ObjectId($data->id);
               $friend_id = new \MongoDB\BSON\ObjectId($validate['friend_id']);

               $exist = $db->findOne(['_id'=>$friend_id]);
               if (isset($exist)){
                //validation to check if already friends
                // if (Friends::where('user_id', $data->id)->value('friend_id')==$request->friend_id
                // ||Friends::where('friend_id', $request->friend_id)->value('user_id')==$data->id){
                //     return response()->json(
                //         [
                //             'Message'=>"You are already friends"
                //         ],400
                //     );
                // }

                //validation to check the requesting friend exist in user table and user can't make friend himself
                $friend=array(
                    '_id'=>new \MongoDB\BSON\ObjectId(),
                    'friend_id'=>$friend_id,
                );
                $result = $db->updateOne(['_id'=>$user_id],['$push'=>['friends'=>$friend]]);
                    if ($result) {
                        return response()->success('Friend added successfully!',200);
                    } else {
                        return response()->error('Database error!!',400);
                    }
                }
                else{
                    return response()->error('User does not exist!',400);
                }

     }
}
