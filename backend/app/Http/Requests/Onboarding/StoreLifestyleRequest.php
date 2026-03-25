<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreLifestyleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'stress_level' => 'nullable|string|max:32',
            'water_intake' => 'nullable|string|max:64',
            'physical_activity_level' => 'nullable|string|max:64',
        ];
    }
}
