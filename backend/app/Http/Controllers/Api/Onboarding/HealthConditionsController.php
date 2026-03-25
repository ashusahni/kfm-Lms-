<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Onboarding\StoreHealthConditionsRequest;
use App\Models\HealthCondition;
use App\User;

class HealthConditionsController extends Controller
{
    public function store(StoreHealthConditionsRequest $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $user->healthConditions()->sync($request->validated()['conditions']);

        $conditionIds = $user->healthConditions()->pluck('health_conditions.id');
        return apiResponse2(1, 'stored', trans('api.public.stored'), ['conditions' => $conditionIds]);
    }

    public function index()
    {
        $list = HealthCondition::orderBy('name')->get(['id', 'name']);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['conditions' => $list]);
    }
}
