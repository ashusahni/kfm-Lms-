<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_medications' => 'nullable|string|max:2000',
            'past_surgeries' => 'nullable|string|max:2000',
            'food_allergies' => 'nullable|string|max:2000',
            'menstrual_history' => 'nullable|string|max:2000',
        ];
    }
}
