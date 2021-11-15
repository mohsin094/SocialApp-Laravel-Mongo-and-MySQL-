<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function post(Request $req)
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
}
