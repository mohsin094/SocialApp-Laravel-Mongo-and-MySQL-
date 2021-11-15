<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends Controller
{


//registration function
    public function registration(Request $req)
    {
        $users = new User;
        $users->name=$req->name;
        $users->email=$req->email;
        $users->password=bcrypt($req->password);

        $result = $users->save();
        if ($result) {
            UserController::sendVerificationLink($req->name, $req->email);
        } else {
            return ["Result"=>"Not registered"];
        }
    }

    //method to send verification link
    public static function sendVerificationLink($name, $email)
    {
        $details = [
                'name' =>$name,
                'Link'=>url('api/verifyemail/'.$email)
            ];

        \Mail::to($email)->send(new \App\Mail\MyTestMail($details));
        dd("verification Email is Sent.");
    }

    //login function
    public function login(Request $request)
    {
        $validate =Validator::make($request->all(), [
            'email' => 'required|email',
            'password'=> 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(
                [
                    "Message"=>"Please check your email or password pattern and try again"
                ],
                200
            );
        }

        if ($user=Auth::attempt(['email' =>  $request->email, 'password' =>  $request->password])) {
            $user = auth()->user();

            if (User::where('id', $user->id)->value('verified')==1) {
                $key = "example_key";
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
                "nbf" => time() +3600,
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
}
