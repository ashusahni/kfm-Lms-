<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\CourseBatch;
use App\Models\Webinar;
use Illuminate\Http\Request;

class CourseBatchController extends Controller
{
    private function getWebinarQuery()
    {
        $user = apiAuth();
        $webinarIds = $user->getManageableWebinarIds();
        if (empty($webinarIds)) {
            return Webinar::whereRaw('1 = 0');
        }
        return Webinar::whereIn('id', $webinarIds);
    }

    public function index($webinar_id)
    {
        $webinar = $this->getWebinarQuery()->findOrFail($webinar_id);

        $batches = CourseBatch::where('webinar_id', $webinar->id)
            ->orderBy('sort_order')
            ->orderBy('start_date')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'name' => $batch->name,
                    'code' => $batch->code,
                    'start_date' => $batch->start_date,
                    'end_date' => $batch->end_date,
                    'capacity' => $batch->capacity,
                    'status' => $batch->status,
                    'sort_order' => $batch->sort_order,
                    'enrolled_count' => $batch->enrolled_count,
                    'is_open' => $batch->isOpen(),
                ];
            });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['batches' => $batches]);
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

        $batch = CourseBatch::create([
            'webinar_id' => $webinar->id,
            'name' => $request->name,
            'code' => $request->filled('code') ? $request->code : null,
            'start_date' => $request->filled('start_date') ? (int) $request->start_date : null,
            'end_date' => $request->filled('end_date') ? (int) $request->end_date : null,
            'capacity' => $request->filled('capacity') ? (int) $request->capacity : null,
            'status' => $request->status,
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        return apiResponse2(1, 'created', trans('api.public.created'), [
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'code' => $batch->code,
                'start_date' => $batch->start_date,
                'end_date' => $batch->end_date,
                'capacity' => $batch->capacity,
                'status' => $batch->status,
                'sort_order' => $batch->sort_order,
                'enrolled_count' => 0,
                'is_open' => $batch->isOpen(),
            ],
        ]);
    }

    public function update(Request $request, $webinar_id, $batch_id)
    {
        $this->getWebinarQuery()->findOrFail($webinar_id);
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
            return apiResponse2(0, 'validation_error', trans('admin/main.end_date_must_after_start') ?? 'End date must be after start date.');
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

        return apiResponse2(1, 'updated', trans('api.public.updated'), [
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'code' => $batch->code,
                'start_date' => $batch->start_date,
                'end_date' => $batch->end_date,
                'capacity' => $batch->capacity,
                'status' => $batch->status,
                'sort_order' => $batch->sort_order,
                'enrolled_count' => $batch->enrolled_count,
                'is_open' => $batch->isOpen(),
            ],
        ]);
    }

    public function destroy($webinar_id, $batch_id)
    {
        $this->getWebinarQuery()->findOrFail($webinar_id);
        $batch = CourseBatch::where('webinar_id', $webinar_id)->findOrFail($batch_id);

        $salesCount = \App\Models\Sale::where('batch_id', $batch_id)->whereNull('refund_at')->count();
        if ($salesCount > 0) {
            return apiResponse2(0, 'has_enrollments', trans('admin/main.batch_has_enrollments') ?? 'Cannot delete batch that has enrollments.');
        }

        $batch->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'));
    }
}
