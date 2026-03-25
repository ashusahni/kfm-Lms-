<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreDietPatternRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'diet_type' => 'nullable|string|in:veg,nonveg,eggetarian',
            'meal_pattern' => 'nullable|string|max:64',
            'breakfast' => 'nullable|string|max:512',
            'lunch' => 'nullable|string|max:512',
            'dinner' => 'nullable|string|max:512',
            'food_cravings' => 'nullable|string|max:512',
            'outside_food_frequency' => 'nullable|string|max:64',
        ];
    }
}
