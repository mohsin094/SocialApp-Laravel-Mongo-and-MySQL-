<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;

class UserController extends Controller
{

//registration function
    public function registration(RegistrationRequest $request)
    {
        $users = new User;

        $validate = $request->validated();
        $users->name=$validate['name'];
        $users->email=$validate['email'];
        $users->password=bcrypt($validate['password']);

        $result = $users->save();
        if ($result) {
            UserController::sendVerificationLink($validate['name'], $validate['email']);
        } else {
            return ["Result"=>"Not registered"];
        }
    }

    //method to send verification link
    public static function sendVerificationLink($name, $email)
    {
        $details = [
                'name' =>$name,
                'Link'=>url('user/verifyemail/'.$email)
            ];

        \Mail::to($email)->send(new \App\Mail\MyTestMail($details));
        dd("verification Email is Sent.");
    }

    //login function
    public function login(LoginRequest $request)
    {
        $validate =$request->validator();

        if ($user=Auth::attempt(['email' =>  $validate['email'], 'password' =>  $validate['password']])) {
            $user = auth()->user();

            if (User::where('id', $user->id)->value('verified')==1) {
                $key = "owt125";
                $data = [
                "id"=>$user->id,
                "name"=>$user->name,
                "email"=>$user->email,
                "password"=>$user->password
            ];
                $payload = array(
                "iss" => "http://localhost.com",
                "aud" => "http://localhost.com",
                "iat" => time(),
                "nbf" => time(),
                "data"=> $data
            );


                $jwt =  JWT::encode($payload, $key, 'HS256');

                $user->remember_token=$jwt;

                User::where("email", $user->email)->update(["remember_token"=>$jwt]);

                $success = [
                "status"=>"success",
                "token"=> $jwt,
                "data"=>$data
            ];
                return response()->json($success);
            } else {
                UserController::sendVerificationLink($user->name, $user->email);
            }
        } else {
            return response()->json([
                    'Message' => "Either email or password was wrong"
                ], 400);
        }
    }


    //function to verify user
    public function verify($email)
    {
        User::where("email", $email)->update(["verified"=>true,"email_verified_at"=>date('Y-m-d H:i:s')]);
        return response()->json([
                    'Message' => "Your account is verified"
                ], 200);
    }


    //logout function
    public function logout(Request $request)
    {
        $token =$request->token;
        $result=User::where("remember_token", $token)->update(["remember_token"=>null]);

        if ($result) {
            return response()->json([
                "Message"=>"Successfully logout"
            ], 200);
        } else {
            return response()->json([
                "Message"=>"Token is incorrect"
            ], 400);
        }
    }

    //function to decode token
    public function decodeToken($token){
        $secret = "owt125";
        $decoded_data = JWT::decode($token,new Key($secret,'HS256'));
        $user_data = $decoded_data->data;
        return $user_data;
    }
}
