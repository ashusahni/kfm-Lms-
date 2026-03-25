<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\CourseHealthLogSetting;
use App\Models\StudentDailyHealthLog;
use App\Models\Webinar;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Fit Karnataka: Daily Health Challenge – student log CRUD, dietician/admin read-only.
 * Contract: see HEALTH_LOG_SPEC.md (repo root). Any new field/endpoint must be added in both backend and frontend and the spec updated.
 * Connected to: course-health-log-settings (validates enabled + custom_fields when webinar_id present).
 */
class StudentDailyHealthLogController extends Controller
{
    public function index(Request $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $query = StudentDailyHealthLog::with(['user:id,full_name,avatar', 'webinar'])
            ->orderBy('log_date', 'desc');

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isTeacher() || $user->isOrganization()) {
            $webinarIds = $user->getManageableWebinarIds();
            $query->whereIn('webinar_id', $webinarIds);
        } else {
            // Admin or any other role (e.g. panel access): only show own logs
            $query->where('user_id', $user->id);
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
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }
        if (!$user->isUser()) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $request->validate([
            'log_date' => 'required', // date string (Y-m-d) or Unix timestamp accepted
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

        $logDateRaw = $request->log_date;
        // DB column is DATE (Y-m-d). Normalize: accept Unix timestamp or date string.
        $logDate = $this->normalizeLogDate($logDateRaw);
        if (!$logDate) {
            return apiResponse2(0, 'invalid_date', 'Invalid log_date. Use Y-m-d (e.g. 2026-02-27) or Unix timestamp.');
        }
        $webinarId = $request->webinar_id ?: null;

        if ($webinarId) {
            $setting = CourseHealthLogSetting::where('webinar_id', $webinarId)->first();
            if ($setting && !$setting->enable_health_log) {
                return apiResponse2(0, 'health_log_disabled', 'Health log is disabled for this course.');
            }
        }

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
            $log->custom_data = $this->sanitizeCustomDataByCourse($request->custom_data, $webinarId);
        }
        $log->save();

        return apiResponse2(1, 'saved', 'OK', $log->fresh(['user', 'webinar']));
    }

    /**
     * Normalize log_date to Y-m-d for MySQL DATE column.
     * Accepts: Unix timestamp (int or string of digits) or date string (Y-m-d, etc).
     */
    private function normalizeLogDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            $ts = (int) $value;
            if ($ts <= 0) {
                return null;
            }
            return Carbon::createFromTimestamp($ts)->format('Y-m-d');
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Keep only custom_data keys that are defined in the course's health log settings (if any).
     */
    private function sanitizeCustomDataByCourse(array $customData, ?int $webinarId): array
    {
        if (!$webinarId) {
            return $customData;
        }
        $setting = CourseHealthLogSetting::where('webinar_id', $webinarId)->first();
        if (!$setting || empty($setting->custom_fields) || !is_array($setting->custom_fields)) {
            return $customData;
        }
        $allowedKeys = array_column($setting->custom_fields, 'key');
        return array_intersect_key($customData, array_fill_keys($allowedKeys, true));
    }

    public function show($id)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $log = StudentDailyHealthLog::with(['user:id,full_name,avatar', 'webinar'])->find($id);
        if (!$log) {
            return apiResponse2(0, 'not_found', 'Log not found.');
        }

        if ($user->isUser() && $log->user_id != $user->id) {
            return apiResponse2(0, 'forbidden', 'Forbidden.');
        }
        if ($user->isTeacher()) {
            $dieticianWebinarIds = $user->getManageableWebinarIds();
            if (!in_array($log->webinar_id, $dieticianWebinarIds)) {
                return apiResponse2(0, 'forbidden', 'Forbidden.');
            }
        }

        return apiResponse2(1, 'ok', 'OK', $log);
    }

    /**
     * Update an existing health log by id (student only; must own the log and not locked).
     */
    public function update(Request $request, $id)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }
        if (!$user->isUser()) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $log = StudentDailyHealthLog::find($id);
        if (!$log) {
            return apiResponse2(0, 'not_found', 'Log not found.');
        }
        if ($log->user_id != $user->id) {
            return apiResponse2(0, 'forbidden', 'Forbidden.');
        }
        if (!$log->isEditable()) {
            return apiResponse2(0, 'locked', 'This log is locked and cannot be edited.');
        }

        $request->validate([
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

        if ($request->has('water_ml')) {
            $log->water_ml = $request->water_ml;
        }
        if ($request->has('meals')) {
            $log->meals = $request->meals;
        }
        if ($request->has('calories')) {
            $log->calories = $request->calories;
        }
        if ($request->has('protein')) {
            $log->protein = $request->protein;
        }
        if ($request->has('carbs')) {
            $log->carbs = $request->carbs;
        }
        if ($request->has('fat')) {
            $log->fat = $request->fat;
        }
        if ($request->has('medicines')) {
            $log->medicines = $request->medicines;
        }
        if ($request->has('activity_minutes')) {
            $log->activity_minutes = $request->activity_minutes;
        }
        if ($request->has('activity_notes')) {
            $log->activity_notes = $request->activity_notes;
        }
        if ($request->has('adherence_score')) {
            $log->adherence_score = $request->adherence_score;
        }
        if ($request->has('custom_data') && is_array($request->custom_data)) {
            $log->custom_data = $this->sanitizeCustomDataByCourse($request->custom_data, $log->webinar_id);
        }
        $log->save();

        return apiResponse2(1, 'updated', 'OK', $log->fresh(['user', 'webinar']));
    }

    /**
     * Delete a health log (student only; must own the log and not locked).
     */
    public function destroy($id)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }
        if (!$user->isUser()) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $log = StudentDailyHealthLog::find($id);
        if (!$log) {
            return apiResponse2(0, 'not_found', 'Log not found.');
        }
        if ($log->user_id != $user->id) {
            return apiResponse2(0, 'forbidden', 'Forbidden.');
        }
        if (!$log->isEditable()) {
            return apiResponse2(0, 'locked', 'This log is locked and cannot be deleted.');
        }

        $log->delete();
        return apiResponse2(1, 'deleted', 'OK', null);
    }

    /**
     * Summary stats for the current user's health logs (or dietician's course logs).
     * Query params: webinar_id, from_date, to_date (same as index).
     */
    public function summary(Request $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $query = StudentDailyHealthLog::query();

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isTeacher() || $user->isOrganization()) {
            $webinarIds = $user->getManageableWebinarIds();
            $query->whereIn('webinar_id', $webinarIds);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('user_id') && $user->isTeacher()) {
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

        $total = (clone $query)->count();
        $avgAdherence = (clone $query)->whereNotNull('adherence_score')->avg('adherence_score');
        $avgWater = (clone $query)->whereNotNull('water_ml')->where('water_ml', '>', 0)->avg('water_ml');
        $avgCalories = (clone $query)->whereNotNull('calories')->where('calories', '>', 0)->avg('calories');
        $uniqueDays = (int) (clone $query)->select(\DB::raw('count(distinct log_date) as c'))->value('c');

        return apiResponse2(1, 'ok', 'OK', [
            'total_entries' => $total,
            'unique_days' => $uniqueDays,
            'avg_adherence_score' => $avgAdherence !== null ? round((float) $avgAdherence, 1) : null,
            'avg_water_ml' => $avgWater !== null ? round((float) $avgWater, 0) : null,
            'avg_calories' => $avgCalories !== null ? round((float) $avgCalories, 0) : null,
        ]);
    }
}
