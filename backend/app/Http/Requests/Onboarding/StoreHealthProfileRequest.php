<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:1|max:150',
            'gender' => 'nullable|string|max:32',
            'height' => 'required|numeric|min:0|max:300',
            'weight' => 'required|numeric|min:0|max:500',
            'city' => 'nullable|string|max:128',
            'occupation' => 'nullable|string|max:128',
            'lifestyle_type' => 'nullable|string|max:32', // normalized to lowercase in controller
            'language' => 'nullable|string|max:64',
        ];
    }
}
