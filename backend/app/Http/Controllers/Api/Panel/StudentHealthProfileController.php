<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\DietPattern;
use App\Models\FileUpload;
use App\Models\HealthProfile;
use App\Models\LifestyleAssessment;
use App\Models\MedicalData;
use App\Models\Sale;
use App\User;

/**
 * Allows a dietician (teacher/organization) to view a student's full health profile
 * (onboarding questionnaire data) for students enrolled in the dietician's assigned courses.
 */
class StudentHealthProfileController extends Controller
{
    /**
     * GET /panel/students/{user_id}/health-profile
     * Returns the same structure as onboarding ProfileController@show for the given student.
     * Dietician must have at least one course in common (student enrolled in dietician's course).
     */
    public function show($userId)
    {
        $dietician = apiAuth();
        if (!$dietician) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        if (!$dietician->isTeacher() && !$dietician->isOrganization()) {
            return apiResponse2(0, 'forbidden', 'Only dieticians can view student health profiles.');
        }

        $manageableWebinarIds = $dietician->getManageableWebinarIds();
        if (empty($manageableWebinarIds)) {
            return apiResponse2(0, 'forbidden', 'You have no assigned courses.');
        }

        $studentId = (int) $userId;
        $isEnrolled = Sale::where('buyer_id', $studentId)
            ->whereNotNull('webinar_id')
            ->whereIn('webinar_id', $manageableWebinarIds)
            ->whereNull('refund_at')
            ->exists();

        if (!$isEnrolled) {
            return apiResponse2(0, 'forbidden', 'Student is not enrolled in any of your courses.');
        }

        $user = User::find($studentId);
        if (!$user) {
            return apiResponse2(0, 'not_found', 'User not found.');
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
            'user' => ['id' => $user->id, 'full_name' => $user->full_name, 'email' => null],
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
