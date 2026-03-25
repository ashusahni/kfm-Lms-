<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUnlockOverride extends Model
{
    protected $table = 'lesson_unlock_overrides';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    /**
     * Unlock for all users (user_id and group_id both null).
     */
    public function isForAllUsers(): bool
    {
        return $this->user_id === null && $this->group_id === null;
    }

    public function webinar()
    {
        return $this->belongsTo(\App\Models\Webinar::class, 'webinar_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
