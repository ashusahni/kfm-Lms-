<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseBatch;
use App\Models\Webinar;
use Illuminate\Http\Request;

class CourseBatchController extends Controller
{
    /**
     * Dedicated "Course batches" page: list all courses with link to manage batches.
     */
    public function batchesIndex(Request $request)
    {
        $this->authorize('admin_webinars_list');

        $query = Webinar::query()->withCount('batches');
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }
        if ($request->filled('q')) {
            $query->whereTranslationLike('title', '%' . $request->get('q') . '%');
        }
        $webinars = $query->orderBy('id')->paginate(20)->appends($request->query());

        $data = [
            'pageTitle' => trans('admin/main.course_batches') ?? 'Course batches',
            'webinars' => $webinars,
        ];

        return view('admin.course_batches.batches_index', $data);
    }

    public function index(Request $request, $webinar_id)
    {
        $this->authorize('admin_webinars_list');

        $webinar = Webinar::findOrFail($webinar_id);

        $query = CourseBatch::where('webinar_id', $webinar_id);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $batches = $query->orderBy('sort_order')->orderBy('start_date')->paginate(20)->appends($request->query());

        $data = [
            'pageTitle' => trans('admin/main.course_batches') ?? 'Course batches',
            'webinar' => $webinar,
            'batches' => $batches,
        ];

        return view('admin.course_batches.index', $data);
    }

    public function create($webinar_id)
    {
        $this->authorize('admin_webinars_list');

        $webinar = Webinar::findOrFail($webinar_id);

        $data = [
            'pageTitle' => trans('admin/main.create_batch') ?? 'Create batch',
            'webinar' => $webinar,
            'batch' => new CourseBatch(['status' => CourseBatch::STATUS_DRAFT, 'sort_order' => 0]),
        ];

        return view('admin.course_batches.create', $data);
    }

    public function store(Request $request, $webinar_id)
    {
        $this->authorize('admin_webinars_list');

        $webinar = Webinar::findOrFail($webinar_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'nullable|integer',
            'end_date' => 'nullable|integer|gte:start_date',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,open,closed,completed',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $startDate = $request->filled('start_date') ? (int) $request->start_date : null;
        $endDate = $request->filled('end_date') ? (int) $request->end_date : null;

        // For challenge courses: if only start_date given, set end_date = start + 7 or 30 days
        if ($webinar->isChallengeCourse() && $startDate !== null && $endDate === null) {
            $days = $webinar->getChallengeDurationDays();
            if ($days) {
                $endDate = $startDate + ($days * 86400);
            }
        }

        CourseBatch::create([
            'webinar_id' => $webinar->id,
            'name' => $request->name,
            'code' => $request->filled('code') ? $request->code : null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'capacity' => $request->filled('capacity') ? (int) $request->capacity : null,
            'status' => $request->status,
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.batch_created') ?? 'Batch created.',
            'status' => 'success',
        ];

        return redirect(getAdminPanelUrl() . '/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
    }

    public function edit($webinar_id, $batch_id)
    {
        $this->authorize('admin_webinars_list');

        $webinar = Webinar::findOrFail($webinar_id);
        $batch = CourseBatch::where('webinar_id', $webinar_id)->findOrFail($batch_id);

        $data = [
            'pageTitle' => trans('admin/main.edit_batch') ?? 'Edit batch',
            'webinar' => $webinar,
            'batch' => $batch,
        ];

        return view('admin.course_batches.edit', $data);
    }

    public function update(Request $request, $webinar_id, $batch_id)
    {
        $this->authorize('admin_webinars_list');

        $webinar = Webinar::findOrFail($webinar_id);
        $batch = CourseBatch::where('webinar_id', $webinar_id)->findOrFail($batch_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'nullable|integer',
            'end_date' => 'nullable|integer',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,open,closed,completed',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $start = $request->filled('start_date') ? (int) $request->start_date : null;
        $end = $request->filled('end_date') ? (int) $request->end_date : null;
        if ($start !== null && $end !== null && $end < $start) {
            return redirect()->back()->withInput()->withErrors(['end_date' => trans('admin/main.end_date_must_after_start') ?? 'End date must be after start date.']);
        }

        // For challenge courses: if only start given, set end = start + 7 or 30 days
        if ($webinar->isChallengeCourse() && $start !== null && $end === null) {
            $days = $webinar->getChallengeDurationDays();
            if ($days) {
                $end = $start + ($days * 86400);
            }
        }

        $batch->update([
            'name' => $request->name,
            'code' => $request->filled('code') ? $request->code : null,
            'start_date' => $start,
            'end_date' => $end,
            'capacity' => $request->filled('capacity') ? (int) $request->capacity : null,
            'status' => $request->status,
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.batch_updated') ?? 'Batch updated.',
            'status' => 'success',
        ];

        return redirect(getAdminPanelUrl() . '/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
    }

    public function destroy($webinar_id, $batch_id)
    {
        $this->authorize('admin_webinars_list');

        $batch = CourseBatch::where('webinar_id', $webinar_id)->findOrFail($batch_id);

        $salesCount = \App\Models\Sale::where('batch_id', $batch_id)->whereNull('refund_at')->count();
        if ($salesCount > 0) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('admin/main.batch_has_enrollments') ?? 'Cannot delete batch that has enrollments. Close it instead.',
                'status' => 'error',
            ];
            return redirect(getAdminPanelUrl() . '/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
        }

        $batch->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.batch_deleted') ?? 'Batch deleted.',
            'status' => 'success',
        ];

        return redirect(getAdminPanelUrl() . '/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
    }
}
