<?php

namespace CleaniqueCoders\Inviteable\Http\Middleware;

use Closure;
use CleaniqueCoders\Inviteable\Models\Invite;

class Inviteable
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
        $token = $request->route('token');
       
        if(Invite::activeToken($token)->first()) {
            return $next($request);    
        } 

        return redirect()->route(config('inviteable.redirect.middleware'));
    }
}
