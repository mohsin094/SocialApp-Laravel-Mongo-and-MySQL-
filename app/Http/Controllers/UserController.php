<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MongoDB\Client as connection;

class UserController extends Controller
{
//registration function
    public function registration(RegistrationRequest $req)
    {
        $validate = $req->validated();

            $email =$validate["email"];
            $db =(new connection)->socialApp->users;
            $check = $db->findOne(['email'=>$email]);
            if($check){
                return response()->json([
                    'Message' => "User email already registered!"
                ], 400);
            }
            else{
            $db->insertOne([
            'name' => $validate['name'],
            'email' => $validate["email"],
            'password'=>bcrypt($validate["password"]),
            ]);

        if ($db) {
             UserController::sendVerificationLink($req->name, $req->email);
             return response()->json([
                'Message' => "Verification email is send!"
            ], 200);
        } else {
            return response()->json([
                'Message' => "Database error!"
            ], 400);
        }
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
    }

    //login function
    public function login(Request $request)
    {
        $validate =Validator::make($request->all(), [
            'email' => 'required|email',
            'password'=> 'required',
        ]);
        if ($validate->fails()) {
            return response()->json( $validate->errors()->toJson(),400);
        }
        $db =(new connection)->socialApp->users;
        if ($user = $db->findOne(['email'=>$request->email])) {
            if (isset($user['verified'])) {
                $key = "owt125";
                $data = [
                "id"=>(string)$user['_id'],
                "name"=>$user['name'],
                "email"=>$user['email'],
                "password"=>$user['password']
            ];
                $payload = array(
                "iss" => "http://localhost.com",
                "aud" => "http://localhost.com",
                "iat" => time(),
                "nbf" => time(),
                "data"=> $data
            );


                $jwt =  JWT::encode($payload, $key, 'HS256');
                $db->updateOne(['email'=>$request->email],  ['$set' => ['remember_token' => $jwt]]);
                $success = [
                "status"=>"success",
                "token"=> $jwt,
                "data"=>$data
            ];
                return response()->json($success);
            } else {
                UserController::sendVerificationLink($user->name, $user->email);
                return response()->json([
                    'Message' => "Verification email is send!"
                ], 200);

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
        $db =(new connection)->socialApp->users;
        $db->updateOne(['email'=>$email],  ['$set' => ['verified' => 1]]);
        //User::where("email", $email)->update(["verified"=>true,"email_verified_at"=>date('Y-m-d H:i:s')]);
        return response()->json([
                    'Message' => "Your account is verified"
                ], 200);
    }


    //logout function
    public function logout(Request $request)
    {
        $token =$request->bearerToken();
        $db = (new Connection)->socialApp->users;
        $result=$db->updateOne(['remember_token'=>$token],['$unset'=>['remember_token'=>null]]);
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
