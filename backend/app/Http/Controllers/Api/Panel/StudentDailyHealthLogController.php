<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\StudentDailyHealthLog;
use App\Models\Webinar;
use Illuminate\Http\Request;

/**
 * Fit Karnataka: Daily Health Challenge – student log CRUD, instructor/admin read-only.
 */
class StudentDailyHealthLogController extends Controller
{
    public function index(Request $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $query = StudentDailyHealthLog::with(['user:id,full_name,avatar', 'webinar:id,title'])
            ->orderBy('log_date', 'desc');

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isTeacher()) {
            $webinarIds = Webinar::where('teacher_id', $user->id)->pluck('id');
            $query->whereIn('webinar_id', $webinarIds);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }
        if ($request->has('webinar_id')) {
            $query->where('webinar_id', (int) $request->webinar_id);
        }
        if ($request->has('from_date')) {
            $query->where('log_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('log_date', '<=', $request->to_date);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $items = $query->paginate($perPage);

        return apiResponse2(1, 'list', 'OK', $items);
    }

    public function store(Request $request)
    {
        $user = apiAuth();
        if (!$user || !$user->isUser()) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $request->validate([
            'log_date' => 'required|date',
            'webinar_id' => 'nullable|exists:webinars,id',
            'water_ml' => 'nullable|integer|min:0',
            'meals' => 'nullable|array',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|integer|min:0',
            'carbs' => 'nullable|integer|min:0',
            'fat' => 'nullable|integer|min:0',
            'medicines' => 'nullable|string|max:2000',
            'activity_minutes' => 'nullable|integer|min:0',
            'activity_notes' => 'nullable|string|max:500',
            'adherence_score' => 'nullable|integer|min:0|max:100',
            'custom_data' => 'nullable|array',
        ]);

        $logDate = $request->log_date;
        $webinarId = $request->webinar_id ?: null;

        $log = StudentDailyHealthLog::firstOrNew([
            'user_id' => $user->id,
            'webinar_id' => $webinarId,
            'log_date' => $logDate,
        ]);

        if (!$log->isEditable()) {
            return apiResponse2(0, 'locked', 'This log is locked and cannot be edited.');
        }

        $log->water_ml = $request->water_ml ?? $log->water_ml;
        $log->meals = $request->meals ?? $log->meals;
        $log->calories = $request->calories ?? $log->calories;
        $log->protein = $request->protein ?? $log->protein;
        $log->carbs = $request->carbs ?? $log->carbs;
        $log->fat = $request->fat ?? $log->fat;
        $log->medicines = $request->medicines ?? $log->medicines;
        $log->activity_minutes = $request->activity_minutes ?? $log->activity_minutes;
        $log->activity_notes = $request->activity_notes ?? $log->activity_notes;
        $log->adherence_score = $request->adherence_score ?? $log->adherence_score;
        if ($request->has('custom_data') && is_array($request->custom_data)) {
            $log->custom_data = $request->custom_data;
        }
        $log->created_at = $log->created_at ?: time();
        $log->updated_at = time();
        $log->save();

        return apiResponse2(1, 'saved', 'OK', $log->fresh(['user', 'webinar']));
    }

    public function show($id)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $log = StudentDailyHealthLog::with(['user:id,full_name,avatar', 'webinar:id,title'])->find($id);
        if (!$log) {
            return apiResponse2(0, 'not_found', 'Log not found.');
        }

        if ($user->isUser() && $log->user_id != $user->id) {
            return apiResponse2(0, 'forbidden', 'Forbidden.');
        }
        if ($user->isTeacher()) {
            $teacherWebinarIds = Webinar::where('teacher_id', $user->id)->pluck('id');
            if (!$teacherWebinarIds->contains($log->webinar_id)) {
                return apiResponse2(0, 'forbidden', 'Forbidden.');
            }
        }

        return apiResponse2(1, 'ok', 'OK', $log);
    }
}
