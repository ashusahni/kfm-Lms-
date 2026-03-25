<?php

namespace App\Models;

use App\Models\Webinar;
use App\User;
use Illuminate\Database\Eloquent\Model;

class CourseIntake extends Model
{
    protected $table = 'course_intakes';

    protected $fillable = [
        'user_id',
        'webinar_id',
        'weight_history',
        'past_dieting_attempts',
        'digestive_issues',
        'sleep_quality',
        'stress_level',
        'food_preference',
        'meal_timings',
        'daily_schedule',
        'blood_reports',
        'body_measurements',
        'body_photos',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function webinar()
    {
        return $this->belongsTo(Webinar::class);
    }
}
