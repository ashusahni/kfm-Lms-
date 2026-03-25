<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\Api\Sale;
use Illuminate\Http\Request;

/**
 * List students (unique buyers) for the logged-in dietician/teacher.
 */
class StudentsListController extends Controller
{
    /**
     * GET /panel/students
     * Returns unique students enrolled in the dietician's courses (teacher/organization only).
     */
    public function index(Request $request)
    {
        $user = apiAuth();
        if (!$user->isTeacher() && !$user->isOrganization()) {
            return apiResponse2(0, 'forbidden', 'Only dieticians can list students.');
        }

        $webinarIds = $user->getManageableWebinarIds();
        if (empty($webinarIds)) {
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
                'students' => [],
            ]);
        }

        // Include all enrolments in courses the dietician manages (created, teaching, or assigned)
        $sales = Sale::whereNotNull('webinar_id')
            ->whereIn('webinar_id', $webinarIds)
            ->whereNull('refund_at')
            ->with(['buyer', 'webinar'])
            ->orderBy('created_at', 'desc')
            ->get();

        $byBuyer = [];
        foreach ($sales as $sale) {
            $bid = $sale->buyer_id;
            if (!isset($byBuyer[$bid])) {
                $buyer = $sale->buyer;
                $byBuyer[$bid] = [
                    'id' => $buyer ? $buyer->id : $bid,
                    'full_name' => $buyer ? $buyer->full_name : 'Unknown',
                    'email' => null,
                    'avatar' => $buyer ? $buyer->getAvatar(80) : null,
                    'programs' => [],
                ];
            }
            $webinarTitle = $sale->webinar ? $sale->webinar->title : null;
            if ($webinarTitle && !in_array($webinarTitle, $byBuyer[$bid]['programs'])) {
                $byBuyer[$bid]['programs'][] = $webinarTitle;
            }
        }

        $students = array_values($byBuyer);

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'students' => $students,
        ]);
    }
}
