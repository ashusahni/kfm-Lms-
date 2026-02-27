<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Fit Karnataka: Daily Health Challenge – student's log per day (optionally per program).
 * Contract: see HEALTH_LOG_SPEC.md (repo root). New fillable/casts must be reflected in frontend types and API validation.
 */
class StudentDailyHealthLog extends Model
{
    protected $table = 'student_daily_health_logs';

    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    protected $dateFormat = 'U';

    protected $fillable = [
        'user_id',
        'webinar_id',
        'log_date',
        'water_ml',
        'meals',
        'calories',
        'protein',
        'carbs',
        'fat',
        'medicines',
        'activity_minutes',
        'activity_notes',
        'adherence_score',
        'locked_at',
        'custom_data',
    ];

    protected $casts = [
        'meals' => 'array',
        'custom_data' => 'array',
        'log_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'webinar_id');
    }

    /**
     * Check if the log is still editable by the student (before lock time).
     */
    public function isEditable(): bool
    {
        if (empty($this->locked_at)) {
            return true;
        }
        return time() < $this->locked_at;
    }
}
