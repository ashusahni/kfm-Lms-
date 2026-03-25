<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Models\HealthProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HealthProfileController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = apiAuth();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 'unauthorized',
                    'message' => trans('auth.unauthorized'),
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'age' => 'required|integer|min:18|max:80',
                'gender' => 'required|string|in:male,female,other',
                'height' => 'required|numeric|min:100|max:250',
                'weight' => 'required|numeric|min:30|max:300',
                'city' => 'nullable|string|max:128',
                'occupation' => 'nullable|string|max:128',
                'lifestyle_type' => 'nullable|string|in:sedentary,moderately_active,very_active',
                'language' => 'nullable|string|max:64',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 'validation_error',
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            if (!empty($data['gender'])) {
                $data['gender'] = strtolower(trim($data['gender']));
            }
            if (!empty($data['name'])) {
                $user->update(['full_name' => $data['name']]);
            }
            unset($data['name']);

            if (!empty($data['lifestyle_type'])) {
                $data['lifestyle_type'] = strtolower(trim($data['lifestyle_type']));
            }

            $profile = HealthProfile::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data, ['user_id' => $user->id])
            );

            return response()->json([
                'success' => true,
                'status' => 'stored',
                'message' => trans('api.public.stored'),
                'data' => ['health_profile' => $profile->fresh()],
            ]);
        } catch (\Throwable $e) {
            Log::error('HealthProfileController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'status' => 'server_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        return $this->store($request);
    }

    public function show()
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $profile = HealthProfile::where('user_id', $user->id)->first();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['health_profile' => $profile]);
    }
}
