<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Onboarding\StoreBodyGoalsRequest;
use App\Models\BodyGoal;

class GoalController extends Controller
{
    public function store(StoreBodyGoalsRequest $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $user->bodyGoals()->sync($request->validated()['goals']);

        $goalIds = $user->bodyGoals()->pluck('body_goals.id');
        return apiResponse2(1, 'stored', trans('api.public.stored'), ['goals' => $goalIds]);
    }

    public function index()
    {
        $list = BodyGoal::orderBy('name')->get(['id', 'name']);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['goals' => $list]);
    }
}
