<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\CourseHealthLogSetting;
use App\Models\Webinar;
use Illuminate\Http\Request;

/**
 * Returns course health log settings for the frontend form (tracking notes, custom fields).
 * User should only request settings for courses they have access to (e.g. purchased).
 */
class CourseHealthLogSettingController extends Controller
{
    public function show(Request $request, $webinar_id)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $webinar = Webinar::find($webinar_id);
        if (!$webinar) {
            return apiResponse2(0, 'not_found', 'Course not found.');
        }

        // Optional: restrict to enrolled users only (uncomment if you want strict check)
        // if ($user->isUser()) {
        //     $hasAccess = Sale::where('buyer_id', $user->id)->where('webinar_id', $webinar_id)->whereNull('refund_at')->exists();
        //     if (!$hasAccess) {
        //         return apiResponse2(0, 'forbidden', 'You do not have access to this course.');
        //     }
        // }

        $setting = CourseHealthLogSetting::where('webinar_id', $webinar_id)->first();

        $data = [
            'webinar_id' => (int) $webinar_id,
            'enable_health_log' => $setting ? $setting->enable_health_log : true,
            'tracking_notes' => $setting ? $setting->tracking_notes : null,
            'custom_fields' => $setting && !empty($setting->custom_fields) ? $setting->custom_fields : [],
        ];

        return apiResponse2(1, 'ok', 'OK', $data);
    }
}
