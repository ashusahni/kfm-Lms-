<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Per-course health log configuration: tracking notes (from description) and optional custom fields.
 */
class CourseHealthLogSetting extends Model
{
    protected $table = 'course_health_log_settings';

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $fillable = [
        'webinar_id',
        'enable_health_log',
        'tracking_notes',
        'custom_fields',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'enable_health_log' => 'boolean',
        'custom_fields' => 'array',
    ];

    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'webinar_id', 'id');
    }
}
