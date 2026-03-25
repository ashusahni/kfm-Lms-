<?php

namespace App\Http\Middleware\Api;

use Closure;

class CheckApiKey
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
        // Payment gateway callbacks (e.g. Razorpay redirect after payment) are browser
        // redirects and cannot send x-api-key. Verification is done via signature/server-side.
        if (str_contains($request->path(), 'payments/verify')) {
            return $next($request);
        }

        $expectedKey = config('app.api_key');
        $givenKey = $request->header('x-api-key');

        if (empty($expectedKey) || (string) $givenKey !== (string) $expectedKey) {
            return apiResponse2(0, 'client_identity_error', 'client identification failed.check the api key');
        }
        return $next($request);
    }
}
