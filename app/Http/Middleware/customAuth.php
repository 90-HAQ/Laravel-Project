<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $req, Closure $next)
    {
        $token = $req->token;

        if(!empty($token))
        {
            $data = DB::table('users')->where('remember_token', $token)->first();

            if(!empty($data))
            {
                return $next($req->merge(['user_data' => $data]));
            }
            else
            {
                return response()->json(['Message' => 'Your are not Authenticated User.'], 401);
            }
        }
        else
        {
            return response()->json(['Message' => 'Your Token is Empty.'], 404);
        }
        
    }
}
