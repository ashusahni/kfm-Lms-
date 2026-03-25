<?php

namespace App\Models\Api\Traits;

trait CheckWebinarItemAccessTrait
{
    public function canViewError()
    {
        $error = null;
        $user = apiAuth();
        if (!$user) {
            $error = trans('public.not_login_toast_msg_lang');
        } elseif (!$this->webinar->checkUserHasBought($user)) {
            $error = trans('public.not_access_to_this_content');
        } elseif ($lessonError = $this->getLessonUnlockError($user)) {
            $error = $lessonError;
        } elseif ($checkSequenceContent = $this->checkSequenceContent($user)) {
            $errors = [];
            if (is_array($checkSequenceContent)) {
                foreach ($checkSequenceContent as $key => $value) {
                    if ($value) {
                        $errors[] = $value;
                    }
                }
            }
            $error = (count($errors) > 0) ? implode(' ', $errors) : null;
        } elseif (!$this->user_has_access) {
            $error = trans('public.not_access_to_this_content');
        }

        return $error;
    }

    /**
     * Check admin-defined lesson unlock rules. Returns error message if content is locked.
     */
    protected function getLessonUnlockError($user)
    {
        $contentType = null;
        if ($this instanceof \App\Models\Api\File) {
            $contentType = 'file';
        } elseif ($this instanceof \App\Models\Api\Session) {
            $contentType = 'session';
        } elseif ($this instanceof \App\Models\Api\TextLesson) {
            $contentType = 'text_lesson';
        }
        if (!$contentType || !$this->webinar_id || !$this->id) {
            return null;
        }
        $service = app(\App\Services\LessonUnlockService::class);
        if ($service->isContentUnlocked($user, (int) $this->webinar_id, $contentType, (int) $this->id)) {
            return null;
        }
        $status = $service->getUnlockStatus($user, (int) $this->webinar_id, $contentType, (int) $this->id);
        return $status['message'] ?? trans('public.not_access_to_this_content');
    }
}
