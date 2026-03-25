<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentDailyHealthLog;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin health log: student daily health logs (aligned with frontend panel).
 * Lists all student logs with stats, filters, export; view single log; link to course settings.
 * System health checks moved to SystemHealthController at /admin/system-health.
 */
class HealthLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $query = StudentDailyHealthLog::with(['user:id,full_name,email', 'webinar'])
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
        $chartData = $this->getChartData($request);
        // Title is in webinar_translations, not webinars table
        $locale = app()->getLocale();
        $courseIds = StudentDailyHealthLog::whereNotNull('webinar_id')->distinct()->pluck('webinar_id');
        $coursesWithLogs = collect();
        if ($courseIds->isNotEmpty()) {
            $coursesWithLogs = \DB::table('webinars')
                ->whereIn('webinars.id', $courseIds)
                ->leftJoin('webinar_translations', function ($j) use ($locale) {
                    $j->on('webinars.id', '=', 'webinar_translations.webinar_id')
                        ->where('webinar_translations.locale', '=', $locale);
                })
                ->select('webinars.id', \DB::raw('COALESCE(webinar_translations.title, CONCAT("Course #", webinars.id)) as title'))
                ->orderBy('title')
                ->get();
        }

        $data = [
            'pageTitle' => trans('admin/main.health_log') ?? 'Health Log',
            'logs' => $logs,
            'stats' => $stats,
            'chartData' => $chartData,
            'coursesWithLogs' => $coursesWithLogs,
        ];

        return view('admin.health_log.index', $data);
    }

    public function show($id)
    {
        $this->authorize('admin_general_dashboard_show');

        $log = StudentDailyHealthLog::with(['user:id,full_name,email,avatar', 'webinar'])->findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/main.student_health_log') ?? 'Student health log',
            'log' => $log,
        ];

        return view('admin.health_log.show', $data);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('admin_general_dashboard_show');

        $query = StudentDailyHealthLog::with(['user:id,full_name,email', 'webinar'])
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
                'Medicines', 'Activity (min)', 'Activity notes', 'Adherence %', 'Meals', 'Custom data', 'Created',
            ]);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->id,
                    optional($log->user)->full_name ?? $log->user_id,
                    optional($log->user)->email ?? '',
                    optional($log->webinar)->title ?? ($log->webinar_id ?: '—'),
                    $log->log_date ? (\Carbon\Carbon::parse($log->log_date)->format('Y-m-d')) : '',
                    $log->water_ml ?? '',
                    $log->calories ?? '',
                    $log->protein ?? '',
                    $log->carbs ?? '',
                    $log->fat ?? '',
                    $log->medicines ?? '',
                    $log->activity_minutes ?? '',
                    $log->activity_notes ?? '',
                    $log->adherence_score ?? '',
                    $log->meals ? json_encode($log->meals) : '',
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

    public function exportJson(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $query = StudentDailyHealthLog::with(['user:id,full_name,email', 'webinar'])
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
        $filename = 'student_health_logs_' . date('Y-m-d_His') . '.json';

        return response()->streamDownload(
            function () use ($logs) {
                echo json_encode(['data' => $logs->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            },
            $filename,
            ['Content-Type' => 'application/json', 'Content-Disposition' => 'attachment; filename="' . $filename . '"']
        );
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
        $avgWater = (clone $base)->whereNotNull('water_ml')->where('water_ml', '>', 0)->avg('water_ml');
        $avgCalories = (clone $base)->whereNotNull('calories')->where('calories', '>', 0)->avg('calories');

        return [
            'total' => $total,
            'with_course' => $withCourse,
            'avg_adherence' => $avgAdherence ? round($avgAdherence, 1) : null,
            'unique_users' => $uniqueUsers,
            'avg_water' => $avgWater ? round($avgWater, 0) : null,
            'avg_calories' => $avgCalories ? round($avgCalories, 0) : null,
        ];
    }

    protected function getChartData(Request $request): array
    {
        $days = 14;
        $end = $request->filled('to_date') ? strtotime($request->to_date . ' 23:59:59') : time();
        $start = $request->filled('from_date') ? strtotime($request->from_date . ' 00:00:00') : ($end - ($days * 86400));
        $rangeStart = $end - ($days * 86400);
        if ($start < $rangeStart) {
            $start = $rangeStart;
        }

        $query = StudentDailyHealthLog::query()
            ->where('log_date', '>=', date('Y-m-d', $start))
            ->where('log_date', '<=', date('Y-m-d', $end));

        if ($request->filled('webinar_id')) {
            $query->where('webinar_id', (int) $request->webinar_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        $logs = $query->get();

        $labels = [];
        $countData = [];
        $adherenceData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', $end - $i * 86400);
            $labels[] = date('M j', strtotime($day));
            $dayLogs = $logs->filter(function ($log) use ($day) {
                $logDate = $log->log_date;
                $dateStr = $logDate instanceof \Carbon\Carbon
                    ? $logDate->format('Y-m-d')
                    : (\is_string($logDate) ? $logDate : date('Y-m-d', strtotime($logDate)));
                return $dateStr === $day;
            });
            $countData[] = $dayLogs->count();
            $avg = $dayLogs->whereNotNull('adherence_score')->avg('adherence_score');
            $adherenceData[] = $avg !== null ? round($avg, 0) : null;
        }

        return [
            'labels' => $labels,
            'count' => $countData,
            'adherence' => $adherenceData,
        ];
    }
}
