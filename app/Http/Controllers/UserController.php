<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\key;
//import custom services
use App\Services\DbConnection;
use App\Services\Authentication;
use App\Services\VerificationLink;

class UserController extends Controller
{
//registration function
    public function registration(RegistrationRequest $req)
    {
        try{
            $validate = $req->validated();
            $db =(new DbConnection('users'))->getConnection();
            $check = $db->findOne(['email'=>$validate["email"]]);
            if($check){
                return response()->error('Invalid Email!',400);
            }
            else{

                $inserted = $db->insertOne([
                'name' => $validate['name'],
                'email' => $validate["email"],
                'password'=>bcrypt($validate["password"]),
                 ]);

                if (isset($inserted)) {
                    new VerificationLink($req->name, $req->email);
                    return response()->success('Verification email is send!',200);
                } else {
                    return response()->error('Database error!',400);
                }
            }
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }

    //login function
    public function login(LoginRequest $request)
    {
        try{
            $validate = $request->validated();

            $db =(new DbConnection('users'))->getConnection();
            $userDate = $db->findOne(['email'=>$validate['email']]);
            if(!isset($userDate)){
                return response()->error('Wrong credentials!',400);
            }

            if (Hash::check($validate['password'], $userDate->password)) {
                    if (isset($userDate['verified'])) {
                    //call to Authentication service to generate token
                    $authentication = new Authentication($userDate);
                    $jwt = $authentication->getToken();
                        $db->updateOne(['email'=>$validate['email']],  ['$set' => ['remember_token' => $jwt]]);
                        $success = [
                        "token"=> $jwt,
                        "data"=>$userDate
                    ];
                        return response()->success($success,200);
                    } else {
                        new VerificationLink($userDate->name, $userDate->email);
                        return response()->success('Verification email is send!',200);
                    }
            } else{
            return response()->error('Wrong credentials!',400);
            }

        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }
    //logout function
    public function logout(Request $request)
    {
        try{
            $token = $request->bearerToken();
            $db =(new DbConnection('users'))->getConnection();
            $result=$db->updateOne(['remember_token'=>$token],['$unset'=>['remember_token'=>null]]);
            if ($result) {
                return response()->success('Successfully logout!',200);
            } else {
                return response()->success('Token expire!',400);
            }
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }

    //function to verify user
    public function verify($email)
    {
        try{
            $dbObj = new DbConnection('users');
            $db = $dbObj->getConnection();
            $db->updateOne(['email'=>$email],  ['$set' => ['verified' => 1]]);
            //User::where("email", $email)->update(["verified"=>true,"email_verified_at"=>date('Y-m-d H:i:s')]);
            return response()->success('Your account is verified!',200);
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }

    //function to decode token
    public function decodeToken($token){
        try{
            $secret = "owt125";
            $decoded_data = JWT::decode($token,new Key($secret,'HS256'));
            $user_data = $decoded_data->data;
            return $user_data;
        }catch(\Exception $e){
            return response()->error($e->getMessage(),400);
        }
    }
}
