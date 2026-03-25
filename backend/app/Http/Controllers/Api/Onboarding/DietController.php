<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Onboarding\StoreDietPatternRequest;
use App\Models\DietPattern;

class DietController extends Controller
{
    public function store(StoreDietPatternRequest $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        $diet = DietPattern::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return apiResponse2(1, 'stored', trans('api.public.stored'), ['diet_pattern' => $diet->fresh()]);
    }
}
