<?php

namespace App\Http\Middleware;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\key;
use Closure;
use Illuminate\Http\Request;

class UerAuthentication
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
        if (User::where("remember_token", $token)->exists()){
            try{
                $secret = config('constant.secret_key');
                $decoded_data = JWT::decode($token,new Key($secret,'HS256'));
                $user_data = $decoded_data->data;
                $request=$request->merge(array('data' => $user_data));
                return $next($request);
            }catch(\Exception $e){
                return response()->error($e->getMessage(),400);
            }
        }else{
            return response()->json([
                'Message' => "Token expire!"
            ], 400);
        }
       
    }
}
