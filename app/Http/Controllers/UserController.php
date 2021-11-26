<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Services\Authentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs;
use App\Jobs\SendEmailJob;

class UserController extends Controller
{

    //registration function
    public function registration(RegistrationRequest $request)
    {
        $users = new User;

        $validate = $request->validated();
        $users->name = $validate['name'];
        $users->email = $validate['email'];
        $users->password = bcrypt($validate['password']);

        $result = $users->save();
        if ($result) 
        {
            UserController::sendVerificationLink($validate['name'], $validate['email']);
        } else {
            return response()->error('Registration failed!', 400);
        }
    }

    //method to send verification link
    public static function sendVerificationLink($name, $email)
    {
        $details = [
            'name' => $name,
            'Link' => url('user/verifyemail/'.$email)
        ];
    
       dispatch(new SendEmailJob($details,$email));
        return response()->success('verification Email is Sent.', 200);
    }

    //login function
    public function login(LoginRequest $request)
    {
        $validate = $request->validated();

        if ($user = Auth::attempt(['email' => $validate['email'], 'password' => $validate['password']])) {
            $user = auth()->user();

            if (User::where('id', $user->id)->value('verified') == 1) {
                $authentication = new Authentication($user);
                $jwt = $authentication->getToken();

                $user->remember_token = $jwt;
                User::where("email", $user->email)->update(["remember_token" => $jwt]);
                $success = [
                    "status" => "success",
                    "token" => $jwt,
                    "data" => $user,
                ];
                return response()->success($success, 200);
            } else {
                UserController::sendVerificationLink($user->name, $user->email);
            }
        } else {
            return response()->error('Wrong credential!', 400);
        }
    }

    //function to verify user
    public function verify($email)
    {
        User::where("email", $email)->update(["verified" => true, "email_verified_at" => date('Y-m-d H:i:s')]);
        return response()->success('Your account is verified', 200);
    }

    //logout function
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $result = User::where("remember_token", $token)->update(["remember_token" => null]);
        if ($result) {
            return response()->success('Successfully logout', 200);
        } else {
            return response()->error('Token expire!', 400);
        }
    }
}
