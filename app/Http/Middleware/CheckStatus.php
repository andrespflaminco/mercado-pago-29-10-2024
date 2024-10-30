<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatus
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

      if(isset(auth()->user()->email_verified_at)) {
        $email_verificado = auth()->user()->email_verified_at;
      } else {
        $email_verificado = now();
      }
        /*
      if(auth()->check() && (auth()->user()->confirmed == 0 ) && (  now() > (($email_verificado)->addDays(7) ) )  ) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

      return \Redirect::to("regist");


      }
        */
        
        return $next($request);
    }
}
