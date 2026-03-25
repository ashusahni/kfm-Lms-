<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CourseBatch;
use App\Models\Webinar;
use Illuminate\Http\Request;

class CourseBatchController extends Controller
{
    private function getWebinarQuery()
    {
        $user = auth()->user();
        return Webinar::where(function ($q) use ($user) {
            if ($user->isTeacher()) {
                $q->where('teacher_id', $user->id);
            } elseif ($user->isOrganization()) {
                $q->where('creator_id', $user->id);
            } else {
                $q->whereRaw('1 = 0');
            }
        });
    }

    public function index(Request $request, $webinar_id)
    {
        $webinar = $this->getWebinarQuery()->findOrFail($webinar_id);

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

        return view(getTemplate() . '.panel.webinar.batches.index', $data);
    }

    public function create($webinar_id)
    {
        $webinar = $this->getWebinarQuery()->findOrFail($webinar_id);

        $data = [
            'pageTitle' => trans('admin/main.create_batch') ?? 'Create batch',
            'webinar' => $webinar,
            'batch' => new CourseBatch(['status' => CourseBatch::STATUS_DRAFT, 'sort_order' => 0]),
        ];

        return view(getTemplate() . '.panel.webinar.batches.create', $data);
    }

    public function store(Request $request, $webinar_id)
    {
        $webinar = $this->getWebinarQuery()->findOrFail($webinar_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'nullable|integer',
            'end_date' => 'nullable|integer|gte:start_date',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,open,closed,completed',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        CourseBatch::create([
            'webinar_id' => $webinar->id,
            'name' => $request->name,
            'code' => $request->filled('code') ? $request->code : null,
            'start_date' => $request->filled('start_date') ? (int) $request->start_date : null,
            'end_date' => $request->filled('end_date') ? (int) $request->end_date : null,
            'capacity' => $request->filled('capacity') ? (int) $request->capacity : null,
            'status' => $request->status,
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        $toastData = ['title' => trans('public.request_success'), 'msg' => trans('admin/main.batch_created') ?? 'Batch created.', 'status' => 'success'];
        return redirect('/panel/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
    }

    public function edit($webinar_id, $batch_id)
    {
        $webinar = $this->getWebinarQuery()->findOrFail($webinar_id);
        $batch = CourseBatch::where('webinar_id', $webinar_id)->findOrFail($batch_id);

        $data = [
            'pageTitle' => trans('admin/main.edit_batch') ?? 'Edit batch',
            'webinar' => $webinar,
            'batch' => $batch,
        ];

        return view(getTemplate() . '.panel.webinar.batches.edit', $data);
    }

    public function update(Request $request, $webinar_id, $batch_id)
    {
        $webinar = $this->getWebinarQuery()->findOrFail($webinar_id);
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

        $batch->update([
            'name' => $request->name,
            'code' => $request->filled('code') ? $request->code : null,
            'start_date' => $start,
            'end_date' => $end,
            'capacity' => $request->filled('capacity') ? (int) $request->capacity : null,
            'status' => $request->status,
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        $toastData = ['title' => trans('public.request_success'), 'msg' => trans('admin/main.batch_updated') ?? 'Batch updated.', 'status' => 'success'];
        return redirect('/panel/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
    }

    public function destroy($webinar_id, $batch_id)
    {
        $this->getWebinarQuery()->findOrFail($webinar_id);
        $batch = CourseBatch::where('webinar_id', $webinar_id)->findOrFail($batch_id);

        $salesCount = \App\Models\Sale::where('batch_id', $batch_id)->whereNull('refund_at')->count();
        if ($salesCount > 0) {
            $toastData = ['title' => trans('public.request_failed'), 'msg' => trans('admin/main.batch_has_enrollments') ?? 'Cannot delete batch that has enrollments. Close it instead.', 'status' => 'error'];
            return redirect('/panel/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
        }

        $batch->delete();
        $toastData = ['title' => trans('public.request_success'), 'msg' => trans('admin/main.batch_deleted') ?? 'Batch deleted.', 'status' => 'success'];
        return redirect('/panel/webinars/' . $webinar_id . '/batches')->with(['toast' => $toastData]);
    }
}
