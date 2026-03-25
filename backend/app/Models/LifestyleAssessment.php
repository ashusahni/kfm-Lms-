<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class LifestyleAssessment extends Model
{
    protected $table = 'lifestyle_assessments';

    protected $fillable = [
        'user_id', 'sleep_hours', 'stress_level', 'water_intake', 'physical_activity_level',
    ];

    protected $casts = [
        'sleep_hours' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
