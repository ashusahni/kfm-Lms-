<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\CourseIntake\StoreCourseIntakeRequest;
use App\Models\CourseIntake;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseIntakeController extends Controller
{
    /**
     * Ensure the authenticated user has purchased the course (webinar).
     */
    private function ensureUserHasAccess($webinarId)
    {
        $user = apiAuth();
        if (!$user) {
            return [false, response()->json(['success' => false, 'status' => 'unauthorized', 'message' => 'Unauthorized'], 401)];
        }
        $webinar = Webinar::find($webinarId);
        if (!$webinar) {
            return [false, response()->json(['success' => false, 'status' => 'not_found', 'message' => 'Course not found'], 404)];
        }
        if (!$webinar->checkUserHasBought($user)) {
            return [false, response()->json(['success' => false, 'status' => 'not_purchased', 'message' => 'You must purchase this course to access the intake form'], 403)];
        }
        return [true, $user];
    }

    /**
     * GET /panel/webinars/{webinarId}/intake - Get intake form for a course (only if purchased).
     */
    public function show($webinarId)
    {
        [$ok, $result] = $this->ensureUserHasAccess($webinarId);
        if (!$ok) {
            return $result;
        }
        $user = $result;

        $intake = CourseIntake::where('user_id', $user->id)->where('webinar_id', $webinarId)->first();
        $data = $intake ? $intake->toArray() : null;
        if ($intake) {
            $data['blood_reports_urls'] = $this->pathsToUrls($intake->blood_reports);
            $data['body_measurements_url'] = $intake->body_measurements ? Storage::disk('public')->url($intake->body_measurements) : null;
            $data['body_photos_urls'] = $this->pathsToUrls($intake->body_photos);
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'intake' => $data,
            'webinar' => ['id' => (int) $webinarId, 'title' => Webinar::find($webinarId)->title ?? null],
        ]);
    }

    /**
     * POST /panel/webinars/{webinarId}/intake - Create or update intake (only if purchased).
     */
    public function store(Request $request, $webinarId)
    {
        [$ok, $result] = $this->ensureUserHasAccess($webinarId);
        if (!$ok) {
            return $result;
        }
        $user = $result;

        $validator = Validator::make($request->all(), [
            'weight_history' => 'nullable|string|max:2000',
            'past_dieting_attempts' => 'nullable|string|max:2000',
            'digestive_issues' => 'nullable|string|max:2000',
            'sleep_quality' => 'nullable|string|max:128',
            'stress_level' => 'nullable|string|max:128',
            'food_preference' => 'nullable|string|max:2000',
            'meal_timings' => 'nullable|string|max:1000',
            'daily_schedule' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $intake = CourseIntake::updateOrCreate(
                ['user_id' => $user->id, 'webinar_id' => $webinarId],
                $validator->validated()
            );

            return apiResponse2(1, 'stored', trans('api.public.stored'), ['intake' => $intake->fresh()]);
        } catch (\Throwable $e) {
            Log::error('CourseIntakeController@store: ' . $e->getMessage());
            return response()->json(['success' => false, 'status' => 'server_error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /panel/webinars/{webinarId}/intake/upload - Upload files (blood reports, body measurements, body photos).
     */
    public function upload(Request $request, $webinarId)
    {
        [$ok, $result] = $this->ensureUserHasAccess($webinarId);
        if (!$ok) {
            return $result;
        }
        $user = $result;

        $request->validate([
            'blood_reports.*' => 'nullable|file|max:10240',
            'body_measurements' => 'nullable|file|max:10240',
            'body_photos.*' => 'nullable|image|max:5120',
        ]);

        try {
            $intake = CourseIntake::firstOrCreate(
                ['user_id' => $user->id, 'webinar_id' => $webinarId],
                ['user_id' => $user->id, 'webinar_id' => $webinarId]
            );

            $baseDir = 'course-intakes/' . $user->id . '/' . $webinarId;

            if ($request->hasFile('blood_reports')) {
                $paths = [];
                foreach ($request->file('blood_reports') as $file) {
                    $paths[] = $file->store($baseDir . '/blood-reports', 'public');
                }
                $existing = $intake->blood_reports ? explode(',', $intake->blood_reports) : [];
                $intake->blood_reports = implode(',', array_merge($existing, $paths));
            }
            if ($request->hasFile('body_measurements')) {
                if ($intake->body_measurements) {
                    Storage::disk('public')->delete($intake->body_measurements);
                }
                $intake->body_measurements = $request->file('body_measurements')->store($baseDir . '/measurements', 'public');
            }
            if ($request->hasFile('body_photos')) {
                $paths = [];
                foreach ($request->file('body_photos') as $file) {
                    $paths[] = $file->store($baseDir . '/body-photos', 'public');
                }
                $existing = $intake->body_photos ? explode(',', $intake->body_photos) : [];
                $intake->body_photos = implode(',', array_merge($existing, $paths));
            }

            $intake->save();

            $urls = [
                'blood_reports' => $this->pathsToUrls($intake->blood_reports),
                'body_measurements' => $intake->body_measurements ? Storage::disk('public')->url($intake->body_measurements) : null,
                'body_photos' => $this->pathsToUrls($intake->body_photos),
            ];

            return apiResponse2(1, 'stored', trans('api.public.stored'), ['files' => $urls]);
        } catch (\Throwable $e) {
            Log::error('CourseIntakeController@upload: ' . $e->getMessage());
            return response()->json(['success' => false, 'status' => 'server_error', 'message' => $e->getMessage()], 500);
        }
    }

    private function pathsToUrls($pathsStr)
    {
        if (!$pathsStr) {
            return [];
        }
        $paths = array_map('trim', explode(',', $pathsStr));
        return array_values(array_filter(array_map(function ($p) {
            return $p ? Storage::disk('public')->url($p) : null;
        }, $paths)));
    }
}
