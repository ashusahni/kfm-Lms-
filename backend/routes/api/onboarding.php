<?php

use Illuminate\Support\Facades\Route;

/*
| Onboarding: health questionnaire after signup. All routes require api.auth.
*/
Route::group([
    'prefix' => 'onboarding',
    'namespace' => 'Onboarding',
    'middleware' => ['api.auth', 'api.request.type'],
], function () {
    Route::get('/profile', ['uses' => 'ProfileController@show']);
    Route::post('/health-profile', ['uses' => 'HealthProfileController@store']);
    Route::put('/health-profile', ['uses' => 'HealthProfileController@update']);
    Route::get('/health-profile', ['uses' => 'HealthProfileController@show']);
    Route::post('/medical-data', ['uses' => 'MedicalController@store']);
    Route::post('/diet-pattern', ['uses' => 'DietController@store']);
    Route::post('/lifestyle', ['uses' => 'LifestyleController@store']);
    Route::post('/body-goals', ['uses' => 'GoalController@store']);
    Route::post('/upload-files', ['uses' => 'FileUploadController@store']);

    // Lists for multi-select (conditions & goals)
    Route::get('/health-conditions', ['uses' => 'HealthConditionsController@index']);
    Route::post('/health-conditions', ['uses' => 'HealthConditionsController@store']);
    Route::get('/body-goals', ['uses' => 'GoalController@index']);
});
