<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Lightweight health check (no DB). Use to verify backend is up. */
Route::get('/up', function () {
    return response()->json(['status' => 'ok', 'service' => 'backend'], 200);
});

/* Root "/" without any middleware that touches DB, so it works even when DB is empty or unreachable. */
Route::get('/', function () {
    $url = config('frontend.url');
    if (!empty($url) && !config('frontend.serve_react', false)) {
        return redirect()->away(rtrim($url, '/') . '/');
    }
    return response()->json([
        'ok' => true,
        'message' => 'Backend is running. Set FRONTEND_URL in env to redirect the app to your frontend.',
        'api' => url('/api/development'),
    ], 200);
})->middleware([]);

Route::group(['prefix' => 'my_api', 'namespace' => 'Api\Panel', 'middleware' => 'signed', 'as' => 'my_api.web.'], function () {
    Route::get('checkout/{user}', 'CartController@webCheckoutRender')->name('checkout');
    Route::get('/charge/{user}', 'PaymentsController@webChargeRender')->name('charge');
    Route::get('/subscribe/{user}/{subscribe}', 'SubscribesController@webPayRender')->name('subscribe');
    Route::get('/registration_packages/{user}/{package}', 'RegistrationPackagesController@webPayRender')->name('registration_packages');
});

Route::group(['prefix' => 'api_sessions'], function () {
    Route::get('/big_blue_button', ['uses' => 'Api\Panel\SessionsController@BigBlueButton'])->name('big_blue_button');
    Route::get('/agora', ['uses' => 'Api\Panel\SessionsController@agora'])->name('agora');

});

Route::get('/mobile-app', 'Web\MobileAppController@index')->middleware(['share'])->name('mobileAppRoute');
Route::get('/maintenance', 'Web\MaintenanceController@index')->middleware(['share'])->name('maintenanceRoute');

/* Emergency Database Update */
Route::get('/emergencyDatabaseUpdate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true
    ]);
    $msg1 = \Illuminate\Support\Facades\Artisan::output();

    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--force' => true
    ]);
    $msg2 = \Illuminate\Support\Facades\Artisan::output();

    \Illuminate\Support\Facades\Artisan::call('clear:all', [
        '--force' => true
    ]);

    return response()->json([
        'migrations' => $msg1,
        'sections' => $msg2,
    ]);
});

Route::group(['namespace' => 'Auth', 'middleware' => ['check_mobile_app', 'share', 'check_maintenance']], function () {
    Route::get('/login', function () { return spaOrRedirectToFrontend(); });
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');
    Route::get('/register', function () { return spaOrRedirectToFrontend(); });
    Route::post('/register', 'RegisterController@register');
    Route::post('/register/form-fields', 'RegisterController@getFormFieldsByUserType');
    Route::get('/verification', 'VerificationController@index');
    Route::post('/verification', 'VerificationController@confirmCode');
    Route::get('/verification/resend', 'VerificationController@resendCode');
    Route::get('/forget-password', 'ForgotPasswordController@showLinkRequestForm');
    Route::post('/forget-password', 'ForgotPasswordController@forgot');
    Route::get('reset-password/{token}', 'ResetPasswordController@showResetForm');
    Route::post('/reset-password', 'ResetPasswordController@updatePassword');
    Route::get('/google', 'SocialiteController@redirectToGoogle');
    Route::get('/google/callback', 'SocialiteController@handleGoogleCallback');
    Route::get('/facebook/redirect', 'SocialiteController@redirectToFacebook');
    Route::get('/facebook/callback', 'SocialiteController@handleFacebookCallback');
    // Referral (paid add-on) - Removed: Route::get('/reff/{code}', 'ReferralController@referral');
});

Route::group(['namespace' => 'Web', 'middleware' => ['check_mobile_app', 'impersonate', 'share', 'check_maintenance']], function () {
    Route::get('/stripe', function () {
        return view('web.default.cart.channels.stripe');
    });

    // When React is served from this backend (SERVE_REACT_FROM_BACKEND=true), serve the SPA for any unmatched GET that is not admin/api
    if (config('frontend.serve_react', false)) {
        Route::get('{path?}', 'SpaController')
            ->where('path', '^(?!admin$|admin/|api$|api/|my_api|api_sessions|emergencyDatabaseUpdate|mobile-app|maintenance).*')
            ->defaults('path', '')
            ->name('spa.catchall');
    }

    // When frontend runs on a separate URL (e.g. React on :8080), redirect app traffic there so only /admin and /api stay on this backend
    $frontendUrl = config('frontend.url');

    Route::fallback(function () use ($frontendUrl) {
        if (request()->isMethod('GET') && !empty($frontendUrl) && !config('frontend.serve_react', false)) {
            $path = request()->path();
            $backendOnlyPrefixes = ['admin', 'api', 'my_api', 'api_sessions', 'emergencyDatabaseUpdate', 'mobile-app', 'maintenance', 'captcha', 'stripe'];
            $isBackendOnly = false;
            foreach ($backendOnlyPrefixes as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    $isBackendOnly = true;
                    break;
                }
            }
            if (!$isBackendOnly) {
                return redirect()->away(rtrim($frontendUrl, '/') . '/' . $path . (request()->getQueryString() ? '?' . request()->getQueryString() : ''));
            }
        }
        return view("errors.404", ['pageTitle' => trans('public.error_404_page_title')]);
    });

    // set Locale
    Route::post('/locale', 'LocaleController@setLocale')->name('appLocaleRoute');

    // set Locale
    Route::post('/set-currency', 'SetCurrencyController@setCurrency');

    Route::get('/', function () {
        return spaOrRedirectToFrontend();
    });

    Route::get('/getDefaultAvatar', 'DefaultAvatarController@make');

    Route::group(['prefix' => 'course'], function () {
        Route::get('/{slug}', function () { return spaOrRedirectToFrontend(); });
        Route::get('/{slug}/file/{file_id}/download', 'WebinarController@downloadFile');
        Route::get('/{slug}/file/{file_id}/showHtml', 'WebinarController@showHtmlFile');
        Route::get('/{slug}/lessons/{lesson_id}/read', 'WebinarController@getLesson');
        Route::post('/getFilePath', 'WebinarController@getFilePath');
        Route::get('/{slug}/file/{file_id}/play', 'WebinarController@playFile');
        Route::get('/{slug}/free', 'WebinarController@free');
        Route::get('/{slug}/points/apply', 'WebinarController@buyWithPoint');
        Route::post('/{id}/report', 'WebinarController@reportWebinar');
        Route::post('/{id}/learningStatus', 'WebinarController@learningStatus');

        Route::group(['middleware' => 'web.auth'], function () {
            // Installments (paid add-on) - Removed: Route::get('/{slug}/installments', ...);
            Route::post('/learning/itemInfo', 'LearningPageController@getItemInfo');
            Route::get('/learning/{slug}', 'LearningPageController@index');
            Route::get('/learning/{slug}/noticeboards', 'LearningPageController@noticeboards');
            Route::get('/assignment/{assignmentId}/download/{id}/attach', 'LearningPageController@downloadAssignment');
            Route::post('/assignment/{assignmentId}/history/{historyId}/message', 'AssignmentHistoryController@storeMessage');
            Route::post('/assignment/{assignmentId}/history/{historyId}/setGrade', 'AssignmentHistoryController@setGrade');
            Route::get('/assignment/{assignmentId}/history/{historyId}/message/{messageId}/downloadAttach', 'AssignmentHistoryController@downloadAttach');

            /* Course forum - Removed
            Route::group(['prefix' => '/learning/{slug}/forum'], function () {
                ...
            });
            */

            Route::post('/direct-payment', 'WebinarController@directPayment');
        });
    });

    Route::group(['prefix' => 'certificate_validation'], function () {
        Route::get('/', 'CertificateValidationController@index');
        Route::post('/validate', 'CertificateValidationController@checkValidate');
    });


    Route::group(['prefix' => 'cart'], function () {
        Route::post('/store', 'CartManagerController@store');
        Route::get('/{id}/delete', 'CartManagerController@destroy');
    });

    Route::group(['middleware' => 'web.auth'], function () {

        Route::group(['prefix' => 'laravel-filemanager'], function () {
            \UniSharp\LaravelFilemanager\Lfm::routes();
        });

        Route::group(['prefix' => 'reviews'], function () {
            Route::post('/store', 'WebinarReviewController@store');
            Route::post('/store-reply-comment', 'WebinarReviewController@storeReplyComment');
            Route::get('/{id}/delete', 'WebinarReviewController@destroy');
            Route::get('/{id}/delete-comment/{commentId}', 'WebinarReviewController@destroy');
        });

        Route::group(['prefix' => 'favorites'], function () {
            Route::get('{slug}/toggle', 'FavoriteController@toggle');
            Route::post('/{id}/update', 'FavoriteController@update');
            Route::get('/{id}/delete', 'FavoriteController@destroy');
        });

        Route::group(['prefix' => 'comments'], function () {
            Route::post('/store', 'CommentController@store');
            Route::post('/{id}/reply', 'CommentController@storeReply');
            Route::post('/{id}/update', 'CommentController@update');
            Route::post('/{id}/report', 'CommentController@report');
            Route::get('/{id}/delete', 'CommentController@destroy');
        });

        Route::group(['prefix' => 'cart'], function () {
            Route::get('/', function () { return spaOrRedirectToFrontend(); });

            Route::post('/coupon/validate', 'CartController@couponValidate');
            Route::post('/checkout', 'CartController@checkout')->name('checkout');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/{id}/follow', 'UserController@followToggle');
        });

        Route::group(['prefix' => 'become-instructor'], function () {
            Route::get('/', 'BecomeInstructorController@index')->name('becomeInstructor');
            Route::get('/packages', 'BecomeInstructorController@packages')->name('becomeInstructorPackages');
            Route::get('/packages/{id}/checkHasInstallment', 'BecomeInstructorController@checkPackageHasInstallment');
            Route::get('/packages/{id}/installments', 'BecomeInstructorController@getInstallmentsByRegistrationPackage');
            Route::post('/', 'BecomeInstructorController@store');
            Route::post('/form-fields', 'BecomeInstructorController@getFormFieldsByUserType');
        });

    });

    Route::group(['prefix' => 'meetings'], function () {
        Route::post('/reserve', 'MeetingController@reserve');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/{id}/profile', 'UserController@profile');
        Route::post('/{id}/availableTimes', 'UserController@availableTimes');
        Route::post('/{id}/send-message', 'UserController@sendMessage');
    });

    Route::group(['prefix' => 'payments'], function () {
        Route::post('/payment-request', 'PaymentController@paymentRequest');
        Route::get('/verify/{gateway}', ['as' => 'payment_verify', 'uses' => 'PaymentController@paymentVerify']);
        Route::post('/verify/{gateway}', ['as' => 'payment_verify_post', 'uses' => 'PaymentController@paymentVerify']);
        Route::get('/status', 'PaymentController@payStatus');
        Route::get('/payku/callback/{id}', 'PaymentController@paykuPaymentVerify')->name('payku.result');
    });

    Route::group(['prefix' => 'subscribes'], function () {
        Route::get('/apply/{webinarSlug}', 'SubscribeController@apply');
        Route::get('/apply/bundle/{bundleSlug}', 'SubscribeController@bundleApply');
    });

    Route::group(['prefix' => 'search'], function () {
        Route::get('/', function () { return spaOrRedirectToFrontend(); });
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/{categoryTitle}/{subCategoryTitle?}', 'CategoriesController@index');
    });

    Route::get('/classes', function () { return spaOrRedirectToFrontend(); });

    Route::get('/profile', function () { return spaOrRedirectToFrontend(); });

    Route::get('/reward-courses', 'RewardCoursesController@index');

    Route::group(['prefix' => 'blog'], function () {
        Route::get('/', function () { return spaOrRedirectToFrontend(); });
        Route::get('/categories/{category}', 'BlogController@index');
        Route::get('/{slug}', function () { return spaOrRedirectToFrontend(); });
    });

    Route::group(['prefix' => 'contact'], function () {
        Route::get('/', function () { return spaOrRedirectToFrontend(); });
        Route::post('/store', 'ContactController@store');
    });

    Route::group(['prefix' => 'instructors'], function () {
        Route::get('/', function () { return spaOrRedirectToFrontend(); });
    });

    Route::group(['prefix' => 'organizations'], function () {
        Route::get('/', 'UserController@organizations');
    });

    Route::group(['prefix' => 'load_more'], function () {
        Route::get('/{role}', 'UserController@handleInstructorsOrOrganizationsPage');
    });

    Route::group(['prefix' => 'pages'], function () {
        Route::get('/{link}', 'PagesController@index');
    });

    // Captcha
    Route::group(['prefix' => 'captcha'], function () {
        Route::post('create', function () {
            $response = ['status' => 'success', 'captcha_src' => captcha_src('flat')];

            return response()->json($response);
        });
        Route::get('{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');
    });

    Route::post('/newsletters', 'UserController@makeNewsletter');

    Route::group(['prefix' => 'jobs'], function () {
        Route::get('/{methodName}', 'JobsController@index');
        Route::post('/{methodName}', 'JobsController@index');
    });

    Route::group(['prefix' => 'regions'], function () {
        Route::get('/provincesByCountry/{countryId}', 'RegionController@provincesByCountry');
        Route::get('/citiesByProvince/{provinceId}', 'RegionController@citiesByProvince');
        Route::get('/districtsByCity/{cityId}', 'RegionController@districtsByCity');
    });

    Route::group(['prefix' => 'instructor-finder'], function () {
        Route::get('/', 'InstructorFinderController@index');
        Route::get('/wizard', 'InstructorFinderController@wizard');
    });

    /* Products (Store) - Removed
    Route::group(['prefix' => 'products'], function () {
        ...
    });
    */

    Route::get('/reward-products', 'RewardProductsController@index');

    Route::group(['prefix' => 'bundles'], function () {
        Route::get('/', function () { return spaOrRedirectToFrontend(); });
        Route::get('/{slug}', function () { return spaOrRedirectToFrontend(); });
        Route::get('/{slug}/free', 'BundleController@free');

        Route::group(['middleware' => 'web.auth'], function () {
            Route::get('/{slug}/favorite', 'BundleController@favoriteToggle');
            Route::get('/{slug}/points/apply', 'BundleController@buyWithPoint');

            Route::group(['prefix' => 'reviews'], function () {
                Route::post('/store', 'BundleReviewController@store');
                Route::post('/store-reply-comment', 'BundleReviewController@storeReplyComment');
                Route::get('/{id}/delete', 'BundleReviewController@destroy');
                Route::get('/{id}/delete-comment/{commentId}', 'BundleReviewController@destroy');
            });

            Route::post('/direct-payment', 'BundleController@directPayment');
        });
    });

    /* Forums - Removed
    Route::group(['prefix' => 'forums'], function () {
        ...
    });
    */

    Route::group(['prefix' => 'cookie-security'], function () {
        Route::post('/all', 'CookieSecurityController@setAll');
        Route::post('/customize', 'CookieSecurityController@setCustomize');
    });


    Route::group(['prefix' => 'upcoming_courses'], function () {
        Route::get('/', function () { return spaOrRedirectToFrontend(); });
        Route::get('{slug}', function () { return spaOrRedirectToFrontend(); });
        Route::get('{slug}/toggleFollow', 'UpcomingCoursesController@toggleFollow');
        Route::get('{slug}/favorite', 'UpcomingCoursesController@favorite');
        Route::post('{id}/report', 'UpcomingCoursesController@report');
    });

    /* Installments, Waitlists, Gift (paid add-ons) - Removed
    Route::group(['prefix' => 'installments'], function () {
        Route::group(['middleware' => 'web.auth'], function () {
            Route::get('/request_submitted', 'InstallmentsController@requestSubmitted');
            Route::get('/request_rejected', 'InstallmentsController@requestRejected');
            Route::get('/{id}', 'InstallmentsController@index');
            Route::post('/{id}/store', 'InstallmentsController@store');
        });
    });

    Route::group(['prefix' => 'waitlists'], function () {
        Route::post('/join', 'WaitlistController@store');
    });

    Route::group(['prefix' => 'gift', 'middleware' => 'fit_karnataka.disable:gift'], function () {
        Route::group(['middleware' => 'web.auth'], function () {
            Route::get('/{item_type}/{item_slug}', 'GiftController@index');
            Route::post('/{item_type}/{item_slug}', 'GiftController@store');
        });
    });
    */

    /* Forms */
    Route::get('/forms/{url}', 'FormsController@index');
    Route::post('/forms/{url}/store', 'FormsController@store');

});

