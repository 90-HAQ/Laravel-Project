<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class existingAccount
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
        
        if(!empty($data))
        {
            return response()->json(['Message' => 'Account already exists.'], 302);   
        }
        else
        { 
            return $next($req);
        }
    }
}
