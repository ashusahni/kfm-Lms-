<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Impersonate
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
        if (session()->has('impersonated')) {
            // Use web guard explicitly so the panel and views see the impersonated user
            Auth::guard('web')->onceUsingId(session()->get('impersonated'));
        }

        return $next($request);
    }
}
