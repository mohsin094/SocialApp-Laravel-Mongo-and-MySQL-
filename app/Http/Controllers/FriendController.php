<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Friends;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AddFriendRequest;

class FriendController extends Controller
{
     //post function
     public function addFriend(AddFriendRequest $request)
     {
         $token=$request->bearerToken();
         $validate = $request->validated();
         //check the token exist in user table
         if (User::where("remember_token", $token)->exists()){
                $userObj = new UserController();
                $data =  $userObj->decodeToken($token);
                $friend = new Friends;
                $user =User::find($data->id);
                //$friend->user_id=$data->id;
                $friend->friend_id=$validate['friend_id'];
               if($data->id==$validate['friend_id']){
                return response()->json(
                    [
                        'Message'=>"You can't add yourself"
                    ],400
                );
               }

               if (User::where("id", $validate['friend_id'])->exists()){
                //validation to check if already friends
                if (Friends::where('user_id', $data->id)->value('friend_id')==$validate['friend_id']
                ||Friends::where('friend_id', $validate['friend_id'])->value('user_id')==$data->id){
                    return response()->json(
                        [
                            'Message'=>"You are already friends"
                        ],400
                    );
                }

                //validation to check the requesting friend exist in user table and user can't make friend himself
                    $result = $user->friends()->save($friend);
                    if ($result) {
                        return response()->json(
                            [
                                'Message'=>"Friend added successfully"
                            ],200
                        );
                    } else {
                        return response()->json(
                            [
                                'Error'=>"Error in adding friend"
                            ],400
                        );
                    }
                }
                else{
                    return response()->json(
                        [
                            'Error'=>"Requesting friend does not exist"
                        ],400
                    );
                }

            }
         else {
            return response()->json(
                [
                    'Token error'=>"Token expired!"
                ],400
            );
        }

     }
}
