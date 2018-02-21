<?php

namespace App\Http\Middleware;

use Closure;

class IsHirer
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
        if($request->user()->role != 'hirer') {
            \App::abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
    
}
