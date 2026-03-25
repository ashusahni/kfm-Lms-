<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/home', ['uses' => 'HomeController@index']);

Route::get('/fit-karnataka-config', function () {
    if (!config('fit_karnataka.enabled', false)) {
        return response()->json(['enabled' => false]);
    }
    return response()->json([
        'enabled' => true,
        'disable' => config('fit_karnataka.disable', []),
        'terminology' => config('fit_karnataka.terminology', []),
    ]);
});

/*
|--------------------------------------------------------------------------
| Mobile / Flutter API (versioned)
|--------------------------------------------------------------------------
| Use base path /api/mobile/v1 for Flutter app. All other API calls
| should use /api/development (see config endpoint).
*/
Route::group(['prefix' => 'mobile/v1', 'namespace' => 'Mobile'], function () {
    Route::get('/config', ['uses' => 'MobileConfigController@index']);
    Route::get('/ping', ['uses' => 'MobileConfigController@ping']);
});

Route::group(['prefix' => '/development'], function () {

    Route::get('/', function () {
        return 'api test';
    });

    Route::middleware('api') ->group(base_path('routes/api/auth.php'));

    Route::namespace('Web')->group(base_path('routes/api/guest.php'));

    Route::prefix('panel')->middleware('api.auth')->namespace('Panel')->group(base_path('routes/api/user.php'));

    Route::group(['namespace' => 'Config', 'middleware' => []], function () {
        Route::get('/config', ['uses' => 'ConfigController@list']);
        Route::get('/config/register/{role}', ['uses' => 'ConfigController@registerConfig']);
    });

    Route::prefix('dietician')->middleware(['api.auth', 'api.level-access:teacher'])->namespace('Dietician')->group(base_path('routes/api/dietician.php'));
    // Legacy: instructor prefix redirects to same dietician logic
    Route::prefix('instructor')->middleware(['api.auth', 'api.level-access:teacher'])->namespace('Dietician')->group(base_path('routes/api/dietician.php'));

    Route::group([], base_path('routes/api/onboarding.php'));

    // Admin API: course and lesson unlock rules (admin only)
    Route::prefix('admin')->middleware(['api.auth', 'api.admin'])->namespace('Admin')->group(function () {
        Route::get('courses', function () {
            $webinars = \App\Models\Webinar::where('type', 'course')->orderBy('id', 'desc')->get(['id', 'title', 'slug', 'status', 'created_at']);
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['courses' => $webinars]);
        });
        Route::get('courses/{courseId}', function ($courseId) {
            $webinar = \App\Models\Webinar::find($courseId);
            if (!$webinar) {
                return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
            }
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['course' => $webinar]);
        });
        Route::get('courses/{courseId}/lessons', ['uses' => 'LessonUnlockRuleController@index']);
        Route::get('courses/{courseId}/lessons/{contentType}/{contentId}', ['uses' => 'LessonUnlockRuleController@show']);
        Route::post('courses/{courseId}/lessons', ['uses' => 'LessonUnlockRuleController@store']);
        Route::patch('lessons/{id}', ['uses' => 'LessonUnlockRuleController@update']);
        Route::delete('lessons/{id}', ['uses' => 'LessonUnlockRuleController@destroy']);
        Route::post('courses/{courseId}/lessons/{contentType}/{contentId}/overrides', ['uses' => 'LessonUnlockRuleController@storeOverride']);
        Route::delete('lesson-overrides/{overrideId}', ['uses' => 'LessonUnlockRuleController@destroyOverride']);
        Route::get('courses/{courseId}/lessons/{contentType}/{contentId}/preview', ['uses' => 'LessonUnlockRuleController@previewUnlock']);
    });
});
