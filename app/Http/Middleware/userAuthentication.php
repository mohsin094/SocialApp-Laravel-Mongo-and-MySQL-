<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\key;
use MongoDB\Client as connection;

class userAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token=$request->bearerToken();

        $db = (new connection)->socialApp->users;
        //check the token exist in user table
        if ($db->findOne(['remember_token'=>$token])){
            try{
                $secret = "owt125";
                $decoded_data = JWT::decode($token,new Key($secret,'HS256'));
                $user_data = $decoded_data->data;
                $request=$request->merge(array('data' => $user_data));
                return $next($request);
            }catch(\Exception $e){
                return response()->error($e->getMessage(),400);
            }
        }
        else{
            return response()->error('Token expire!',400);
        }

    }
}
