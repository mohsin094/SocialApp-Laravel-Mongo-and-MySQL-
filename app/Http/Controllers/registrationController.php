<?php

namespace App\Http\Controllers;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class registrationController extends Controller
{


   
   function registration(Request $req){
      $users = new User;
      $users->name=$req->name;
      $users->email=$req->email;
      $users->password=bcrypt($req->password);

      $result = $users->save();

      $user = [
         'title' => 'Mail from ItSolutionStuff.com',
         'body' => 'This is for testing email using smtp'

     ];
      if($result){
         $details = [
            'name' =>$req->name,
            'Link'=>url('api/verifyemail/'.$users->email)
        ];
       
        \Mail::to($req->email)->send(new \App\Mail\MyTestMail($details));
        dd("verification Email is Sent.");

         return ["Result"=>"Successfully registered"];
      }
      else{
         return ["Result"=>"Not registered"];
      }
    
   }

   
   public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'password'=> 'required'
        ]);
        echo "email";
        if($validate = Auth::attempt(['email' => $validate["email"], 'password' => $validate["password"]]))
        {
        
        $user = auth()->user();
        $key = "example_key";
        $data = [
            "id"=>$user->id,
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

         User::where("email",$user->email)->update(["remember_token"=>$jwt]);

         $success = [
             "status"=>"success",
             "token"=> $jwt,
             "data"=>$data
         ];
         return response()->json($success);

        }

        else
            {
                echo "Either email or password was wrong";
            }
}


//function to verify user
   public function verify($email)
   {

            echo "hy";
            echo "$email";
        $users = new User;
        User::where("email",$email)->update(["verified"=>true]);
        $users->save();
                return response()->json([
                    'Message' => "I am token verification"
                ],200);
   }
}
