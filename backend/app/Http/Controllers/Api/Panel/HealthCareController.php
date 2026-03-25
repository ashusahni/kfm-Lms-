<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\CourseIntake;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Health Care: dietician view of students who purchased courses, with option to mark initial conversation.
 * Content unlock = 48 hours after course (intake) form submission, or when dietician marks conversation done.
 * GET /panel/health-care: list courses with their purchasers (sales) and conversation status.
 * POST /panel/health-care/sales/{saleId}/initial-conversation: mark initial conversation done for a sale.
 * GET /panel/health-care/sales/{saleId}/intake: view the student's course intake form (questionnaire + file URLs).
 */
class HealthCareController extends Controller
{
    private const FORTY_EIGHT_HOURS = 48 * 3600;

    /**
     * GET /panel/health-care
     * Returns courses (that the dietician manages) with for each: list of sales (students) with
     * purchase date, intake submitted, initial_conversation_at, content_unlocked (48h after form submission).
     */
    public function index(Request $request)
    {
        $user = apiAuth();
        if (!$user->isTeacher() && !$user->isOrganization()) {
            return apiResponse2(0, 'forbidden', 'Only dieticians can access Health Care.');
        }

        $webinarIds = $user->getManageableWebinarIds();
        if (empty($webinarIds)) {
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
                'courses' => [],
            ]);
        }

        $sales = Sale::whereIn('webinar_id', $webinarIds)
            ->whereNotNull('webinar_id')
            ->where('type', 'webinar')
            ->whereNull('refund_at')
            ->with(['buyer', 'webinar'])
            ->orderBy('created_at', 'desc')
            ->get();

        $intakes = CourseIntake::whereIn('webinar_id', $webinarIds)
            ->whereIn('user_id', $sales->pluck('buyer_id')->unique()->filter()->values())
            ->get()
            ->keyBy(function ($i) {
                return $i->user_id . '_' . $i->webinar_id;
            });

        $byWebinar = [];
        foreach ($sales as $sale) {
            $wid = $sale->webinar_id;
            if (!isset($byWebinar[$wid])) {
                $webinar = $sale->webinar;
                $byWebinar[$wid] = [
                    'webinar_id' => $wid,
                    'title' => $webinar ? $webinar->title : 'Course #' . $wid,
                    'slug' => $webinar ? $webinar->slug : null,
                    'students' => [],
                ];
            }
            $createdAt = (int) $sale->created_at;
            $initialConversationAt = isset($sale->initial_conversation_at) ? (int) $sale->initial_conversation_at : null;
            $intake = $intakes->get($sale->buyer_id . '_' . $wid);
            $intakeSubmitted = (bool) $intake;
            $formSubmittedAt = $intake ? (is_object($intake->updated_at) ? $intake->updated_at->timestamp : strtotime($intake->updated_at)) : null;
            $unlockAfter = $formSubmittedAt !== null ? $formSubmittedAt + self::FORTY_EIGHT_HOURS : null;
            $contentUnlocked = $initialConversationAt !== null && $initialConversationAt > 0
                || ($unlockAfter !== null && time() >= $unlockAfter);

            $buyer = $sale->buyer;
            $byWebinar[$wid]['students'][] = [
                'sale_id' => $sale->id,
                'user_id' => $sale->buyer_id,
                'full_name' => $buyer ? $buyer->full_name : 'Unknown',
                'email' => null,
                'avatar' => $buyer ? $buyer->getAvatar(80) : null,
                'purchased_at' => $createdAt,
                'purchased_at_formatted' => $createdAt ? date('Y-m-d H:i', $createdAt) : null,
                'intake_submitted' => $intakeSubmitted,
                'intake_submitted_at_formatted' => $formSubmittedAt ? date('Y-m-d H:i', $formSubmittedAt) : null,
                'initial_conversation_at' => $initialConversationAt,
                'initial_conversation_done' => $initialConversationAt !== null && $initialConversationAt > 0,
                'content_unlocked' => $contentUnlocked,
                'unlock_after_timestamp' => $unlockAfter ?? 0,
            ];
        }

        $courses = array_values($byWebinar);

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'courses' => $courses,
        ]);
    }

    /**
     * POST /panel/health-care/sales/{saleId}/initial-conversation
     * Mark that the dietician has had the initial conversation with the student for this purchase.
     */
    public function markInitialConversation($saleId)
    {
        $user = apiAuth();
        if (!$user->isTeacher() && !$user->isOrganization()) {
            return apiResponse2(0, 'forbidden', 'Only dieticians can mark initial conversation.');
        }

        $webinarIds = $user->getManageableWebinarIds();
        if (empty($webinarIds)) {
            return apiResponse2(0, 'forbidden', 'You have no assigned courses.');
        }

        $sale = Sale::where('id', $saleId)
            ->whereIn('webinar_id', $webinarIds)
            ->whereNotNull('webinar_id')
            ->where('type', 'webinar')
            ->whereNull('refund_at')
            ->first();

        if (!$sale) {
            return apiResponse2(0, 'not_found', 'Sale not found or you do not manage this course.');
        }

        $sale->initial_conversation_at = time();
        $sale->save();

        return apiResponse2(1, 'updated', 'Initial conversation marked complete. Student can now access course content.', [
            'sale_id' => $sale->id,
            'initial_conversation_at' => $sale->initial_conversation_at,
        ]);
    }

    /**
     * GET /panel/health-care/sales/{saleId}/intake
     * Returns the full course intake form for the student who made this purchase (questionnaire + file URLs).
     * Only dieticians who manage this course can access.
     */
    public function showIntake($saleId)
    {
        $user = apiAuth();
        if (!$user->isTeacher() && !$user->isOrganization()) {
            return apiResponse2(0, 'forbidden', 'Only dieticians can view course intake forms.');
        }

        $webinarIds = $user->getManageableWebinarIds();
        if (empty($webinarIds)) {
            return apiResponse2(0, 'forbidden', 'You have no assigned courses.');
        }

        $sale = Sale::where('id', $saleId)
            ->whereIn('webinar_id', $webinarIds)
            ->whereNotNull('webinar_id')
            ->where('type', 'webinar')
            ->whereNull('refund_at')
            ->with(['buyer', 'webinar'])
            ->first();

        if (!$sale) {
            return apiResponse2(0, 'not_found', 'Sale not found or you do not manage this course.');
        }

        $intake = CourseIntake::where('user_id', $sale->buyer_id)
            ->where('webinar_id', $sale->webinar_id)
            ->first();

        if (!$intake) {
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
                'sale_id' => (int) $saleId,
                'webinar' => ['id' => $sale->webinar_id, 'title' => $sale->webinar ? $sale->webinar->title : null],
                'student' => $sale->buyer ? ['id' => $sale->buyer->id, 'full_name' => $sale->buyer->full_name, 'email' => null] : null,
                'intake' => null,
            ]);
        }

        $data = $intake->toArray();
        $data['blood_reports_urls'] = $this->pathsToUrls($data['blood_reports'] ?? '');
        $data['body_measurements_url'] = !empty($intake->body_measurements)
            ? Storage::disk('public')->url($intake->body_measurements)
            : null;
        $data['body_photos_urls'] = $this->pathsToUrls($intake->body_photos ?? '');

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'sale_id' => (int) $saleId,
            'webinar' => ['id' => $sale->webinar_id, 'title' => $sale->webinar ? $sale->webinar->title : null],
            'student' => $sale->buyer ? ['id' => $sale->buyer->id, 'full_name' => $sale->buyer->full_name, 'email' => null] : null,
            'intake' => $data,
        ]);
    }

    private function pathsToUrls($pathsStr)
    {
        if (!$pathsStr) {
            return [];
        }
        $paths = array_map('trim', explode(',', $pathsStr));
        return array_values(array_filter(array_map(function ($p) {
            return $p ? Storage::disk('public')->url($p) : null;
        }, $paths)));
    }
}
