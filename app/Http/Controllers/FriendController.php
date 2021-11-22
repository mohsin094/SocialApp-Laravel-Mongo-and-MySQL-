<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Friends;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FriendController extends Controller
{
     //post function
     public function addFriend(Request $request)
     {
         $token=$request->bearerToken();

         //check the token exist in user table
         if (User::where("remember_token", $token)->exists()){
                $userObj = new UserController();
                $data =  $userObj->decodeToken($token);
                $friend = new Friends;


                $validate =Validator::make($request->all(), [
                    'friend_id'=>'required|integer',
                    //'body' => 'required|string|between:2,100',
                    //'body' => 'string|mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
                ]);
                if ($validate->fails()) {
                    return response()->json( $validate->errors()->toJson(),400);
                }
                $user =User::find($data->id);
                //$friend->user_id=$data->id;
                $friend->friend_id=$request->friend_id;
               if($data->id==$request->friend_id){
                return response()->json(
                    [
                        'Message'=>"You can't add yourself"
                    ],400
                );
               }

               if (User::where("id", $request->friend_id)->exists()){
                //validation to check if already friends
                if (Friends::where('user_id', $data->id)->value('friend_id')==$request->friend_id
                ||Friends::where('friend_id', $request->friend_id)->value('user_id')==$data->id){
                    return response()->json(
                        [
                            'Message'=>"You are already friends"
                        ],400
                    );
                }

                //validation to check the requesting friend exist in user table and user can't make friend himself
             
                   // $result = $friend->save();
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
