<?php

namespace App\Services;

use App\Models\CourseLearning;
use App\Models\LessonUnlockOverride;
use App\Models\LessonUnlockRule;
use App\Models\QuizzesResult;
use App\Models\Sale;
use App\Models\WebinarAssignmentHistory;
use App\User;

class LessonUnlockService
{
    /**
     * Get the enrollment timestamp for a user in a course (first purchase/sale date).
     * Used as "Day 0" for day-based unlock.
     */
    public function getEnrollmentTimestamp(?User $user, int $webinarId): ?int
    {
        if (!$user) {
            return null;
        }
        $sale = Sale::query()
            ->where('buyer_id', $user->id)
            ->where('webinar_id', $webinarId)
            ->whereNull('refund_at')
            ->where('access_to_purchased_item', true)
            ->orderBy('created_at', 'asc')
            ->first();
        if (!$sale || !$sale->created_at) {
            return null;
        }
        return is_numeric($sale->created_at) ? (int) $sale->created_at : (int) strtotime($sale->created_at);
    }

    /**
     * Get the rule for a content item, or null if no rule (default = unlocked).
     */
    public function getRule(int $webinarId, string $contentType, int $contentId): ?LessonUnlockRule
    {
        return LessonUnlockRule::query()
            ->where('webinar_id', $webinarId)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->first();
    }

    /**
     * Check if there is a manual unlock override for this user (all users, this user, or user's group).
     */
    public function hasManualUnlock(?User $user, int $webinarId, string $contentType, int $contentId): bool
    {
        $q = LessonUnlockOverride::query()
            ->where('webinar_id', $webinarId)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId);

        // Unlock for all (both null)
        $all = (clone $q)->whereNull('user_id')->whereNull('group_id')->exists();
        if ($all) {
            return true;
        }
        if (!$user) {
            return false;
        }
        if ((clone $q)->where('user_id', $user->id)->exists()) {
            return true;
        }
        // Group: if user has a group (e.g. discount group), check
        $groupId = $this->getUserGroupId($user);
        if ($groupId !== null && (clone $q)->where('group_id', $groupId)->exists()) {
            return true;
        }
        return false;
    }

    protected function getUserGroupId(User $user): ?int
    {
        $group = $user->getUserGroup();
        return $group ? ($group->id ?? null) : null;
    }

    /**
     * Check if prerequisite content is completed by the user.
     */
    public function isPrerequisiteCompleted(?User $user, int $webinarId, string $prereqType, int $prereqId): bool
    {
        if (!$user) {
            return false;
        }
        if ($prereqType === LessonUnlockRule::CONTENT_TYPE_FILE || $prereqType === LessonUnlockRule::CONTENT_TYPE_SESSION || $prereqType === LessonUnlockRule::CONTENT_TYPE_TEXT_LESSON) {
            $column = $prereqType === LessonUnlockRule::CONTENT_TYPE_FILE ? 'file_id'
                : ($prereqType === LessonUnlockRule::CONTENT_TYPE_SESSION ? 'session_id' : 'text_lesson_id');
            $value = $prereqType === LessonUnlockRule::CONTENT_TYPE_FILE ? $prereqId
                : ($prereqType === LessonUnlockRule::CONTENT_TYPE_SESSION ? $prereqId : $prereqId);
            return CourseLearning::query()
                ->where('user_id', $user->id)
                ->where($column, $value)
                ->exists();
        }
        if ($prereqType === LessonUnlockRule::CONTENT_TYPE_QUIZ) {
            return QuizzesResult::query()
                ->where('user_id', $user->id)
                ->where('quiz_id', $prereqId)
                ->where('status', QuizzesResult::$passed)
                ->exists();
        }
        if ($prereqType === LessonUnlockRule::CONTENT_TYPE_ASSIGNMENT) {
            return WebinarAssignmentHistory::query()
                ->where('student_id', $user->id)
                ->where('assignment_id', $prereqId)
                ->where('status', WebinarAssignmentHistory::$passed)
                ->exists();
        }
        return false;
    }

    /**
     * Get the timestamp when the prerequisite was completed (for delay_after_completion).
     * Returns null if not completed. Uses CourseLearning for file/session/text_lesson (no created_at on course_learning?).
     */
    public function getPrerequisiteCompletedAt(?User $user, int $webinarId, string $prereqType, int $prereqId): ?int
    {
        if (!$user) {
            return null;
        }
        if (in_array($prereqType, [LessonUnlockRule::CONTENT_TYPE_FILE, LessonUnlockRule::CONTENT_TYPE_SESSION, LessonUnlockRule::CONTENT_TYPE_TEXT_LESSON], true)) {
            $column = $prereqType === LessonUnlockRule::CONTENT_TYPE_FILE ? 'file_id'
                : ($prereqType === LessonUnlockRule::CONTENT_TYPE_SESSION ? 'session_id' : 'text_lesson_id');
            $row = CourseLearning::query()
                ->where('user_id', $user->id)
                ->where($column, $prereqId)
                ->orderBy('id', 'desc')
                ->first();
            return $row && isset($row->created_at) ? (is_numeric($row->created_at) ? (int) $row->created_at : (int) strtotime($row->created_at)) : null;
        }
        if ($prereqType === LessonUnlockRule::CONTENT_TYPE_QUIZ) {
            $row = QuizzesResult::query()
                ->where('user_id', $user->id)
                ->where('quiz_id', $prereqId)
                ->where('status', QuizzesResult::$passed)
                ->orderBy('id', 'desc')
                ->first();
            return $row && isset($row->created_at) ? (is_numeric($row->created_at) ? (int) $row->created_at : (int) strtotime($row->created_at)) : null;
        }
        if ($prereqType === LessonUnlockRule::CONTENT_TYPE_ASSIGNMENT) {
            $row = WebinarAssignmentHistory::query()
                ->where('student_id', $user->id)
                ->where('assignment_id', $prereqId)
                ->where('status', WebinarAssignmentHistory::$passed)
                ->orderBy('id', 'desc')
                ->first();
            return $row && isset($row->created_at) ? (is_numeric($row->created_at) ? (int) $row->created_at : (int) strtotime($row->created_at)) : null;
        }
        return null;
    }

    /**
     * Full unlock status for the frontend.
     * Returns: ['unlocked' => bool, 'message' => string|null, 'unlock_at' => int|null, 'visible' => bool]
     */
    public function getUnlockStatus(?User $user, int $webinarId, string $contentType, int $contentId): array
    {
        $default = [
            'unlocked' => true,
            'message' => null,
            'unlock_at' => null,
            'visible' => true,
        ];

        $rule = $this->getRule($webinarId, $contentType, $contentId);

        if (!$rule) {
            return $default;
        }

        if ($rule->is_visible === false) {
            $default['visible'] = false;
        }
        if ($rule->scheduled_publish_at && time() < (int) $rule->scheduled_publish_at) {
            $default['visible'] = false;
            $default['unlocked'] = false;
            $default['unlock_at'] = (int) $rule->scheduled_publish_at;
            $default['message'] = trans('update.content_scheduled', ['date' => dateTimeFormat((int) $rule->scheduled_publish_at, 'j M Y H:i')]);
            return $default;
        }

        // Admin force lock: only manual overrides can unlock
        if ($rule->is_locked) {
            if ($this->hasManualUnlock($user, $webinarId, $contentType, $contentId)) {
                return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
            }
            $default['unlocked'] = false;
            $default['message'] = trans('public.content_locked_by_admin');
            return $default;
        }

        // Manual unlock type: only overrides grant access
        if ($rule->unlock_type === LessonUnlockRule::UNLOCK_TYPE_MANUAL) {
            if ($this->hasManualUnlock($user, $webinarId, $contentType, $contentId)) {
                return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
            }
            $default['unlocked'] = false;
            $default['message'] = trans('public.content_unlocked_manually_by_admin');
            return $default;
        }

        // Check override first (override can unlock regardless of rule)
        if ($this->hasManualUnlock($user, $webinarId, $contentType, $contentId)) {
            return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
        }

        if ($rule->unlock_type === LessonUnlockRule::UNLOCK_TYPE_NONE) {
            return array_merge($default, ['visible' => $rule->is_visible]);
        }

        if ($rule->unlock_type === LessonUnlockRule::UNLOCK_TYPE_DAY) {
            $enrolledAt = $this->getEnrollmentTimestamp($user, $webinarId);
            if (!$enrolledAt) {
                $default['unlocked'] = false;
                $default['message'] = trans('public.not_access_to_this_content');
                return $default;
            }
            $dayNumber = (int) $rule->unlock_day_number;
            $unlockAt = strtotime("+{$dayNumber} days", strtotime(date('Y-m-d 00:00:00', $enrolledAt)));
            if (time() >= $unlockAt) {
                return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
            }
            $daysLeft = (int) ceil(($unlockAt - time()) / 86400);
            $default['unlocked'] = false;
            $default['unlock_at'] = $unlockAt;
            $default['message'] = $daysLeft <= 1
                ? trans('update.this_content_will_be_accessible_for_you_on_date', ['date' => dateTimeFormat($unlockAt, 'j M Y H:i')])
                : trans('update.unlocks_in_days', ['count' => $daysLeft]);
            return $default;
        }

        if ($rule->unlock_type === LessonUnlockRule::UNLOCK_TYPE_DATE) {
            $unlockAt = (int) $rule->unlock_date;
            if (time() >= $unlockAt) {
                return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
            }
            $default['unlocked'] = false;
            $default['unlock_at'] = $unlockAt;
            $default['message'] = trans('update.this_content_will_be_accessible_for_you_on_date', ['date' => dateTimeFormat($unlockAt, 'j M Y H:i')]);
            return $default;
        }

        if ($rule->unlock_type === LessonUnlockRule::UNLOCK_TYPE_SEQUENTIAL) {
            $prereqType = $rule->prerequisite_content_type;
            $prereqId = $rule->prerequisite_content_id;
            if (!$prereqType || !$prereqId) {
                return array_merge($default, ['visible' => $rule->is_visible]);
            }
            if ($this->isPrerequisiteCompleted($user, $webinarId, $prereqType, $prereqId)) {
                return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
            }
            $default['unlocked'] = false;
            $default['message'] = trans('update.you_should_pass_the_previous_lesson_to_view_this_part');
            return $default;
        }

        if ($rule->unlock_type === LessonUnlockRule::UNLOCK_TYPE_DELAY) {
            $prereqType = $rule->prerequisite_content_type;
            $prereqId = $rule->prerequisite_content_id;
            $delayHours = (int) $rule->delay_after_completion_hours;
            if (!$prereqType || !$prereqId) {
                return array_merge($default, ['visible' => $rule->is_visible]);
            }
            $completedAt = $this->getPrerequisiteCompletedAt($user, $webinarId, $prereqType, $prereqId);
            if (!$completedAt) {
                $default['unlocked'] = false;
                $default['message'] = trans('update.you_should_pass_the_previous_lesson_to_view_this_part');
                return $default;
            }
            $unlockAt = $completedAt + ($delayHours * 3600);
            if (time() >= $unlockAt) {
                return array_merge($default, ['unlocked' => true, 'visible' => $rule->is_visible]);
            }
            $hoursLeft = (int) ceil(($unlockAt - time()) / 3600);
            $default['unlocked'] = false;
            $default['unlock_at'] = $unlockAt;
            $default['message'] = trans('update.unlocks_in_hours', ['count' => $hoursLeft]);
            return $default;
        }

        return array_merge($default, ['visible' => $rule->is_visible]);
    }

    /**
     * Whether the content item should be visible in the course outline for this user.
     */
    public function isContentVisible(?User $user, int $webinarId, string $contentType, int $contentId): bool
    {
        $status = $this->getUnlockStatus($user, $webinarId, $contentType, $contentId);
        return $status['visible'];
    }

    /**
     * Whether the user can access (view/play) the content. Used to block actual lesson view.
     */
    public function isContentUnlocked(?User $user, int $webinarId, string $contentType, int $contentId): bool
    {
        $status = $this->getUnlockStatus($user, $webinarId, $contentType, $contentId);
        return $status['unlocked'];
    }
}
