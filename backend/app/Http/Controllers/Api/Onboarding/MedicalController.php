<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Onboarding\StoreMedicalDataRequest;
use App\Models\MedicalData;

class MedicalController extends Controller
{
    public function store(StoreMedicalDataRequest $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        // If gender is not female, ignore any menstrual-related fields for safety
        $gender = optional($user->healthProfile)->gender;
        if (strtolower((string) $gender) !== 'female') {
            unset($data['menstrual_history']);
        }

        $medical = MedicalData::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return apiResponse2(1, 'stored', trans('api.public.stored'), ['medical_data' => $medical->fresh()]);
    }
}
