<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Models\BodyGoal;
use App\Models\HealthCondition;
use App\Models\DietPattern;
use App\Models\FileUpload;
use App\Models\HealthProfile;
use App\Models\LifestyleAssessment;
use App\Models\MedicalData;

class ProfileController extends Controller
{
    /**
     * GET /profile - full onboarding profile for the authenticated user.
     */
    public function show()
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $profile = HealthProfile::where('user_id', $user->id)->first();
        $conditions = $user->healthConditions()->get(['health_conditions.id', 'health_conditions.name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->toArray();
        $medical = MedicalData::where('user_id', $user->id)->first();
        $diet = DietPattern::where('user_id', $user->id)->first();
        $lifestyle = LifestyleAssessment::where('user_id', $user->id)->first();
        $goals = $user->bodyGoals()->get(['body_goals.id', 'body_goals.name'])
            ->map(fn ($g) => ['id' => $g->id, 'name' => $g->name])->values()->toArray();
        $files = FileUpload::where('user_id', $user->id)->first();

        $fileUrls = null;
        if ($files) {
            $fileUrls = [
                'blood_report' => $files->blood_report ? \Storage::disk('public')->url($files->blood_report) : null,
                'medical_report' => $files->medical_report ? \Storage::disk('public')->url($files->medical_report) : null,
                'medication_prescription' => $files->medication_prescription ? \Storage::disk('public')->url($files->medication_prescription) : null,
            ];
            $bodyPhotos = $files->body_photos ? explode(',', $files->body_photos) : [];
            $fileUrls['body_photos'] = array_map(fn ($p) => \Storage::disk('public')->url(trim($p)), $bodyPhotos);
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'user' => ['id' => $user->id, 'full_name' => $user->full_name, 'email' => $user->email],
            'health_profile' => $profile,
            'health_conditions' => $conditions,
            'medical_data' => $medical,
            'diet_pattern' => $diet,
            'lifestyle_assessment' => $lifestyle,
            'body_goals' => $goals,
            'file_uploads' => $fileUrls,
        ]);
    }
}
