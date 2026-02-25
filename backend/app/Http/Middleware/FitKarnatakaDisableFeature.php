<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Fit Karnataka: block access to a feature when it is disabled.
 */
class FitKarnatakaDisableFeature
{
    public function handle($request, Closure $next, string $feature)
    {
        if (fitKarnatakaFeatureDisabled($feature)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This feature is not available.'], 404);
            }
            abort(404);
        }

        return $next($request);
    }
}
