<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        return $next($request);
        //return $next($request);
        if ( Auth::guard("user")->user()->verified_at != "" ) {
            if ( Auth::guard("user")->user()->is_register == 1 ) {
                return $next($request);
            }else{
                return redirect()->route('get:user:sign_up_pending');
            }

        } else {
            return redirect()->route('get:otp-verify');
        }
    }
}
