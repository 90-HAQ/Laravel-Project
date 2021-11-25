<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class verifiedAccount
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
        
        $email = $req->email;

        $data = DB::table('users')->where('email', $email)->first();

        $email_verify = $data->email_verified_at;
        
        if(!empty($email_verify))
        {
            return $next($req->merge(['user_data' => $data]));   
        }
        else
        { 
            return response()->json(['Message' => 'Email is not verified. Please verify your email first']);   
        }
    }
}
