<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentDailyHealthLog;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin: list, filter, analyze and export student daily health logs (per course).
 */
class StudentDailyHealthLogAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $query = StudentDailyHealthLog::with(['user:id,full_name,email', 'webinar:id,title'])
            ->orderBy('log_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('webinar_id')) {
            $query->where('webinar_id', (int) $request->webinar_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }
        if ($request->filled('from_date')) {
            $query->where('log_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('log_date', '<=', $request->to_date);
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $logs = $query->paginate($perPage)->appends($request->query());

        $stats = $this->getStats($request);
        $coursesWithLogs = Webinar::whereIn('id', StudentDailyHealthLog::whereNotNull('webinar_id')->distinct('webinar_id')->pluck('webinar_id'))
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        $data = [
            'pageTitle' => trans('admin/main.student_health_logs') ?? 'Student health logs',
            'logs' => $logs,
            'stats' => $stats,
            'coursesWithLogs' => $coursesWithLogs,
        ];

        return view('admin.student_daily_health_logs.index', $data);
    }

    public function show($id)
    {
        $this->authorize('admin_general_dashboard_show');

        $log = StudentDailyHealthLog::with(['user:id,full_name,email,avatar', 'webinar:id,title'])->findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/main.student_health_log') ?? 'Student health log',
            'log' => $log,
        ];

        return view('admin.student_daily_health_logs.show', $data);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('admin_general_dashboard_show');

        $query = StudentDailyHealthLog::with(['user:id,full_name,email', 'webinar:id,title'])
            ->orderBy('log_date', 'desc');

        if ($request->filled('webinar_id')) {
            $query->where('webinar_id', (int) $request->webinar_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }
        if ($request->filled('from_date')) {
            $query->where('log_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('log_date', '<=', $request->to_date);
        }

        $logs = $query->limit(10000)->get();
        $filename = 'student_health_logs_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'ID', 'User', 'Email', 'Course', 'Log date', 'Water (ml)', 'Calories', 'Protein', 'Carbs', 'Fat',
                'Medicines', 'Activity (min)', 'Activity notes', 'Adherence %', 'Custom data', 'Created',
            ]);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->id,
                    $log->user->full_name ?? $log->user_id,
                    $log->user->email ?? '',
                    $log->webinar->title ?? ($log->webinar_id ?: '—'),
                    $log->log_date ? $log->log_date->format('Y-m-d') : '',
                    $log->water_ml ?? '',
                    $log->calories ?? '',
                    $log->protein ?? '',
                    $log->carbs ?? '',
                    $log->fat ?? '',
                    $log->medicines ?? '',
                    $log->activity_minutes ?? '',
                    $log->activity_notes ?? '',
                    $log->adherence_score ?? '',
                    $log->custom_data ? json_encode($log->custom_data) : '',
                    $log->created_at ? date('Y-m-d H:i', $log->created_at) : '',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function getStats(Request $request): array
    {
        $base = StudentDailyHealthLog::query();
        if ($request->filled('webinar_id')) {
            $base->where('webinar_id', (int) $request->webinar_id);
        }
        if ($request->filled('user_id')) {
            $base->where('user_id', (int) $request->user_id);
        }
        if ($request->filled('from_date')) {
            $base->where('log_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $base->where('log_date', '<=', $request->to_date);
        }

        $total = (clone $base)->count();
        $withCourse = (clone $base)->whereNotNull('webinar_id')->count();
        $avgAdherence = (clone $base)->whereNotNull('adherence_score')->avg('adherence_score');
        $uniqueUsers = (clone $base)->distinct()->count('user_id');

        return [
            'total' => $total,
            'with_course' => $withCourse,
            'avg_adherence' => $avgAdherence ? round($avgAdherence, 1) : null,
            'unique_users' => $uniqueUsers,
        ];
    }
}
