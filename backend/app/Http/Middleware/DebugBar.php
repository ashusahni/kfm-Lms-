<?php namespace App\Http\Middleware;

use Closure;

class DebugBar
{
    /**
     * Handle an incoming request.
     * Debugbar is disabled by default; only enabled when app_debugbar setting is on.
     * Always disabled for admin panel routes to keep admin fast.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app('debugbar')->disable();

        // Keep admin panel fast: never enable debugbar on admin routes
        $prefix = getAdminPanelUrlPrefix();
        if ($prefix !== '' && ($request->is($prefix . '/*') || $request->is($prefix))) {
            return $next($request);
        }

        if (!empty(getGeneralSettings('app_debugbar'))) {
            app('debugbar')->enable();
        }

        return $next($request);
    }
}
