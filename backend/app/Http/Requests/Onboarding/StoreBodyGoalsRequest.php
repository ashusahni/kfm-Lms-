<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreBodyGoalsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'goals' => 'required|array',
            'goals.*' => 'integer|exists:body_goals,id',
        ];
    }
}
