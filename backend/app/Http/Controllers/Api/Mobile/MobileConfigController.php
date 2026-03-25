<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

/**
 * Configuration and metadata for Flutter / mobile app integration.
 * Use these endpoints to bootstrap the app (base URL, auth, feature flags).
 */
class MobileConfigController extends Controller
{
    /**
     * Mobile/Flutter app configuration.
     * Call once on app start to get base URL, auth type, and feature flags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $baseUrl = rtrim(URL::to('/'), '/');
        $apiBasePath = $baseUrl . '/api/development';

        $data = [
            'app_name' => config('app.name'),
            'app_version' => config('app.version', '1.0'),
            'api_version' => '1',
            'api_base_url' => $apiBasePath,
            'auth' => [
                'type' => 'bearer',
                'header' => 'Authorization',
                'header_value_prefix' => 'Bearer ',
                'api_key_header' => 'x-api-key',
                'api_key_required' => true,
            ],
            'content_type' => 'application/json',
            'accept' => 'application/json',
            'endpoints' => [
                'auth' => [
                    'login' => 'POST ' . $apiBasePath . '/login',
                    'register_step' => 'POST ' . $apiBasePath . '/register/step/{step}',
                    'logout' => 'POST ' . $apiBasePath . '/logout',
                    'forget_password' => 'POST ' . $apiBasePath . '/forget-password',
                    'reset_password' => 'POST ' . $apiBasePath . '/reset-password/{token}',
                    'verification' => 'POST ' . $apiBasePath . '/verification',
                ],
                'guest' => [
                    'courses' => 'GET ' . $apiBasePath . '/courses',
                    'course_detail' => 'GET ' . $apiBasePath . '/courses/{id}',
                    'categories' => 'GET ' . $apiBasePath . '/categories',
                    'featured_courses' => 'GET ' . $apiBasePath . '/featured-courses',
                    'search' => 'GET ' . $apiBasePath . '/search',
                    'config' => 'GET ' . $apiBasePath . '/config',
                ],
                'panel' => [
                    'base_path' => $apiBasePath . '/panel',
                    'quick_info' => 'GET ' . $apiBasePath . '/panel/quick-info',
                    'profile' => 'GET ' . $apiBasePath . '/panel/profile-setting',
                    'webinars_purchases' => 'GET ' . $apiBasePath . '/panel/webinars/purchases',
                    'cart' => 'GET ' . $apiBasePath . '/panel/cart/list',
                    'notifications' => 'GET ' . $apiBasePath . '/panel/notifications',
                ],
            ],
        ];

        if (config('fit_karnataka.enabled', false)) {
            $data['fit_karnataka'] = [
                'enabled' => true,
                'disable' => config('fit_karnataka.disable', []),
                'terminology' => config('fit_karnataka.terminology', []),
            ];
        } else {
            $data['fit_karnataka'] = ['enabled' => false];
        }

        return apiResponse2(1, 'ok', 'Mobile API config', $data);
    }

    /**
     * Simple ping for connectivity check (no auth required).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return apiResponse2(1, 'ok', 'pong', [
            'timestamp' => now()->toIso8601String(),
            'api_version' => '1',
        ]);
    }
}
