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
            $data = DB::table('users')->where('remember_token', $token)->get();

            $wordcount = count($data);

            if($wordcount > 0)
            {
                return $next($req);
            }
            else
            {
                return response(['Message' => 'Your are not Authenticated User.']);
            }
        }
        else
        {
            return response(['Message' => 'Your Token is Empty.']);
        }
        
    }
}
