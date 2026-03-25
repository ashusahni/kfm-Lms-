<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Onboarding\StoreLifestyleRequest;
use App\Models\LifestyleAssessment;

class LifestyleController extends Controller
{
    public function store(StoreLifestyleRequest $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        $lifestyle = LifestyleAssessment::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return apiResponse2(1, 'stored', trans('api.public.stored'), ['lifestyle' => $lifestyle->fresh()]);
    }
}
