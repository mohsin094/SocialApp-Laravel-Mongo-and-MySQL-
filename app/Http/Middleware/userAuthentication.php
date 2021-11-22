<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use MongoDB\Client as connection;
use MongoDB\Operation\FindOne;

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
            return $next($request);
        }
        else{
            return response()->json(
                [
                    'Message'=>"Token expire"
                ],400
            );
        }

    }
}
