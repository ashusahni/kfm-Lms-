<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthConditionsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'conditions' => 'required|array',
            'conditions.*' => 'integer|exists:health_conditions,id',
        ];
    }
}
