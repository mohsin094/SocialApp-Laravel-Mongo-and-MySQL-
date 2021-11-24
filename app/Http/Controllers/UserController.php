<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
//import custom services
use App\Services\DbConnection;

class UserController extends Controller
{
//registration function
    public function registration(RegistrationRequest $req)
    {
        $validate = $req->validated();

            $email =$validate["email"];

            $dbObj = new DbConnection('users');
            $db = $dbObj->getConnection();

            $check = $db->findOne(['email'=>$email]);
            if($check){
                return response()->error('Invalid Email!',400);
            }
            else{
                $db->insertOne([
                'name' => $validate['name'],
                'email' => $validate["email"],
                'password'=>bcrypt($validate["password"]),
            ]);

        if ($db) {
             UserController::sendVerificationLink($req->name, $req->email);
             return response()->success('Verification email is send!',200);
        } else {
            return response()->error('Database error!',400);
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
    public function login(LoginRequest $request)
    {

        $validate = $request->validated();

        $dbObj = new DbConnection('users');
        $db = $dbObj->getConnection();
        $user = $db->findOne(['email'=>$validate['email']]);

        if(!isset($user)){
            return response()->error('Wrong credentials!',400);
        }

        if (Hash::check($validate['password'], $user->password)) {
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
                $db->updateOne(['email'=>$validate['email']],  ['$set' => ['remember_token' => $jwt]]);
                $success = [
                "token"=> $jwt,
                "data"=>$data
            ];
                 return response()->success($success,200);
            } else {
                UserController::sendVerificationLink($user->name, $user->email);
                return response()->success('Verification email is send!',200);

            }
        } else {
            return response()->error('Wrong credentials!',400);
        }
    }


    //function to verify user
    public function verify($email)
    {
        $dbObj = new DbConnection('users');
        $db = $dbObj->getConnection();
        $db->updateOne(['email'=>$email],  ['$set' => ['verified' => 1]]);
        //User::where("email", $email)->update(["verified"=>true,"email_verified_at"=>date('Y-m-d H:i:s')]);
        return response()->success('Your account is verified!',200);
    }


    //logout function
    public function logout(Request $request)
    {
        $token =$request->bearerToken();
        $dbObj = new DbConnection('users');
             $db = $dbObj->getConnection();
        $result=$db->updateOne(['remember_token'=>$token],['$unset'=>['remember_token'=>null]]);
        if ($result) {
            return response()->success('Successfully logout!',200);
        } else {
            return response()->success('Token expire!',400);
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
