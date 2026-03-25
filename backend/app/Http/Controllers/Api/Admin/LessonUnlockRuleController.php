<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Controller;
use App\Models\LessonUnlockOverride;
use App\Models\LessonUnlockRule;
use App\Models\Webinar;
use App\Services\LessonUnlockService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LessonUnlockRuleController extends Controller
{
    /**
     * List all lesson unlock rules for a course.
     * GET /admin/courses/{courseId}/lesson-unlock-rules
     */
    public function index($courseId)
    {
        $webinar = Webinar::find($courseId);
        if (!$webinar) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $rules = LessonUnlockRule::where('webinar_id', $courseId)->get();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['rules' => $rules]);
    }

    /**
     * Get one rule by content type and id.
     * GET /admin/courses/{courseId}/lessons/{contentType}/{contentId}
     */
    public function show($courseId, $contentType, $contentId)
    {
        $this->validateContentType($contentType);
        $rule = LessonUnlockRule::where('webinar_id', $courseId)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->first();
        if (!$rule) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $overrides = LessonUnlockOverride::where('webinar_id', $courseId)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->get();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'rule' => $rule,
            'overrides' => $overrides,
        ]);
    }

    /**
     * Create or update a lesson unlock rule.
     * POST /admin/courses/{courseId}/lessons
     * Body: content_type, content_id, unlock_type, unlock_day_number?, unlock_date?, prerequisite_content_type?, prerequisite_content_id?, delay_after_completion_hours?, is_locked?, is_visible?, scheduled_publish_at?
     */
    public function store(Request $request, $courseId)
    {
        $webinar = Webinar::find($courseId);
        if (!$webinar) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $valid = $request->validate([
            'content_type' => ['required', Rule::in(LessonUnlockRule::$contentTypes)],
            'content_id' => 'required|integer|min:1',
            'unlock_type' => ['required', Rule::in(LessonUnlockRule::$unlockTypes)],
            'unlock_day_number' => 'nullable|integer|min:0',
            'unlock_date' => 'nullable|integer',
            'prerequisite_content_type' => ['nullable', Rule::in(LessonUnlockRule::$contentTypes)],
            'prerequisite_content_id' => 'nullable|integer|min:1',
            'delay_after_completion_hours' => 'nullable|integer|min:0',
            'is_locked' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'scheduled_publish_at' => 'nullable|integer',
        ]);
        $rule = LessonUnlockRule::updateOrCreate(
            [
                'webinar_id' => $courseId,
                'content_type' => $valid['content_type'],
                'content_id' => $valid['content_id'],
            ],
            [
                'unlock_type' => $valid['unlock_type'],
                'unlock_day_number' => $valid['unlock_day_number'] ?? null,
                'unlock_date' => $valid['unlock_date'] ?? null,
                'prerequisite_content_type' => $valid['prerequisite_content_type'] ?? null,
                'prerequisite_content_id' => $valid['prerequisite_content_id'] ?? null,
                'delay_after_completion_hours' => $valid['delay_after_completion_hours'] ?? null,
                'is_locked' => $valid['is_locked'] ?? false,
                'is_visible' => $valid['is_visible'] ?? true,
                'scheduled_publish_at' => $valid['scheduled_publish_at'] ?? null,
            ]
        );
        return apiResponse2(1, 'saved', trans('api.public.saved'), ['rule' => $rule]);
    }

    /**
     * Update a lesson unlock rule (partial).
     * PATCH /admin/lessons/{id}
     */
    public function update(Request $request, $id)
    {
        $rule = LessonUnlockRule::find($id);
        if (!$rule) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $valid = $request->validate([
            'unlock_type' => ['sometimes', Rule::in(LessonUnlockRule::$unlockTypes)],
            'unlock_day_number' => 'nullable|integer|min:0',
            'unlock_date' => 'nullable|integer',
            'prerequisite_content_type' => ['nullable', Rule::in(LessonUnlockRule::$contentTypes)],
            'prerequisite_content_id' => 'nullable|integer|min:1',
            'delay_after_completion_hours' => 'nullable|integer|min:0',
            'is_locked' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'scheduled_publish_at' => 'nullable|integer',
        ]);
        $rule->fill($valid);
        $rule->save();
        return apiResponse2(1, 'saved', trans('api.public.saved'), ['rule' => $rule]);
    }

    /**
     * Delete a lesson unlock rule.
     * DELETE /admin/lessons/{id}
     */
    public function destroy($id)
    {
        $rule = LessonUnlockRule::find($id);
        if (!$rule) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        LessonUnlockOverride::where('webinar_id', $rule->webinar_id)
            ->where('content_type', $rule->content_type)
            ->where('content_id', $rule->content_id)
            ->delete();
        $rule->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'), null);
    }

    /**
     * Add a manual unlock override (unlock for all, or for user, or for group).
     * POST /admin/courses/{courseId}/lessons/{contentType}/{contentId}/overrides
     * Body: user_id? (null = all), group_id?
     */
    public function storeOverride(Request $request, $courseId, $contentType, $contentId)
    {
        $this->validateContentType($contentType);
        $webinar = Webinar::find($courseId);
        if (!$webinar) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $valid = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'group_id' => 'nullable|integer',
        ]);
        $user = apiAuth();
        $override = LessonUnlockOverride::create([
            'webinar_id' => $courseId,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'user_id' => $valid['user_id'] ?? null,
            'group_id' => $valid['group_id'] ?? null,
            'created_at' => time(),
            'created_by' => $user ? $user->id : null,
        ]);
        return apiResponse2(1, 'saved', trans('api.public.saved'), ['override' => $override]);
    }

    /**
     * Remove a manual unlock override.
     * DELETE /admin/lesson-overrides/{overrideId}
     */
    public function destroyOverride($overrideId)
    {
        $override = LessonUnlockOverride::find($overrideId);
        if (!$override) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $override->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'), null);
    }

    /**
     * Preview unlock status for a user (admin).
     * GET /admin/courses/{courseId}/lessons/{contentType}/{contentId}/preview?user_id=123
     */
    public function previewUnlock($courseId, $contentType, $contentId, Request $request)
    {
        $this->validateContentType($contentType);
        $userId = $request->input('user_id');
        $user = $userId ? \App\User::find($userId) : null;
        $service = app(LessonUnlockService::class);
        $status = $service->getUnlockStatus($user, (int) $courseId, $contentType, (int) $contentId);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $status);
    }

    protected function validateContentType($contentType)
    {
        if (!in_array($contentType, LessonUnlockRule::$contentTypes, true)) {
            abort(422, 'Invalid content_type');
        }
    }
}
