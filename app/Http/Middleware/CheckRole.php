<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$role,$role2,$role3,$role4,$role5)
    {
   // dd($request->user()->hasRole($role,$role2,$role3,$role4,$role5));
        if($request->user()->hasRole($role,$role2,$role3,$role4,$role5))
        {
             return $next($request);
        }
        
         return response('no permission',401);
    }
}
