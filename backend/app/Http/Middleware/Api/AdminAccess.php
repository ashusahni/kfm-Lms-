<?php

namespace App\Http\Middleware\Api;

use Closure;

class AdminAccess
{
    public function handle($request, Closure $next)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'), null);
        }
        if (!$user->isAdmin()) {
            return apiResponse2(0, 'forbidden', 'Admin access required', null);
        }
        return $next($request);
    }
}
