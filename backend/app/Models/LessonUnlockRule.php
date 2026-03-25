<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUnlockRule extends Model
{
    const UNLOCK_TYPE_NONE = 'none';
    const UNLOCK_TYPE_DAY = 'day';
    const UNLOCK_TYPE_DATE = 'date';
    const UNLOCK_TYPE_MANUAL = 'manual';
    const UNLOCK_TYPE_SEQUENTIAL = 'sequential';
    const UNLOCK_TYPE_DELAY = 'delay';

    const CONTENT_TYPE_FILE = 'file';
    const CONTENT_TYPE_SESSION = 'session';
    const CONTENT_TYPE_TEXT_LESSON = 'text_lesson';
    const CONTENT_TYPE_QUIZ = 'quiz';
    const CONTENT_TYPE_ASSIGNMENT = 'assignment';

    public static $unlockTypes = [
        self::UNLOCK_TYPE_NONE,
        self::UNLOCK_TYPE_DAY,
        self::UNLOCK_TYPE_DATE,
        self::UNLOCK_TYPE_MANUAL,
        self::UNLOCK_TYPE_SEQUENTIAL,
        self::UNLOCK_TYPE_DELAY,
    ];

    public static $contentTypes = [
        self::CONTENT_TYPE_FILE,
        self::CONTENT_TYPE_SESSION,
        self::CONTENT_TYPE_TEXT_LESSON,
        self::CONTENT_TYPE_QUIZ,
        self::CONTENT_TYPE_ASSIGNMENT,
    ];

    protected $table = 'lesson_unlock_rules';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $guarded = ['id'];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_visible' => 'boolean',
        'unlock_day_number' => 'integer',
        'delay_after_completion_hours' => 'integer',
    ];

    public function webinar()
    {
        return $this->belongsTo(\App\Models\Webinar::class, 'webinar_id', 'id');
    }

    /** Returns override rows for this rule (same webinar + content). */
    public function overrides()
    {
        return LessonUnlockOverride::query()
            ->where('webinar_id', $this->webinar_id)
            ->where('content_type', $this->content_type)
            ->where('content_id', $this->content_id)
            ->get();
    }
}
