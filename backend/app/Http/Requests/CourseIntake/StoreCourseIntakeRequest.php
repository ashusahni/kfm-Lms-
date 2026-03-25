<?php

namespace App\Http\Requests\CourseIntake;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseIntakeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'weight_history' => 'nullable|string|max:2000',
            'past_dieting_attempts' => 'nullable|string|max:2000',
            'digestive_issues' => 'nullable|string|max:2000',
            'sleep_quality' => 'nullable|string|max:128',
            'stress_level' => 'nullable|string|max:128',
            'food_preference' => 'nullable|string|max:2000',
            'meal_timings' => 'nullable|string|max:1000',
            'daily_schedule' => 'nullable|string|max:2000',
        ];
    }
}
