<?php


namespace App\Http\Middleware\Api;

use Closure;

class RequestType
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $contentType = $request->header('Content-Type');
        // Allow "application/json" with or without charset (e.g. "application/json; charset=UTF-8")
        $isJson = $contentType && str_starts_with(trim($contentType), 'application/json');
        if (!$isJson && $request->isMethod('POST')) {
            return apiResponse2(0, 'invalid_content_type', 'Content-Type must be application/json');
        }
        return $next($request);
    }
}
