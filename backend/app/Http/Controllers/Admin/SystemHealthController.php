<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Services\HealthCheckService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin: system health checks (server/database etc). Kept separate from student daily health logs.
 */
class SystemHealthController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $query = $this->buildQuery($request);
        $healthLogs = $query->paginate(15)->appends($request->query());

        $stats = $this->getStats($request);
        $chartData = $this->getChartData($request);

        $data = [
            'pageTitle' => trans('admin/main.system_health') ?? 'System health',
            'healthLogs' => $healthLogs,
            'stats' => $stats,
            'chartData' => $chartData,
        ];

        return view('admin.system_health.index', $data);
    }

    public function show($id)
    {
        $this->authorize('admin_general_dashboard_show');

        $healthLog = HealthLog::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/main.system_health') ?? 'System health',
            'healthLog' => $healthLog,
        ];

        return view('admin.system_health.show', $data);
    }

    public function runCheck(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $service = new HealthCheckService();
        $results = $service->runAll();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => trans('admin/main.health_check_completed') ?? 'Health check completed.',
                'results' => $results,
            ]);
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.health_check_completed') ?? 'Health check completed.',
            'status' => 'success',
        ];
        return redirect(getAdminPanelUrl() . '/system-health')->with(['toast' => $toastData]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('admin_general_dashboard_show');

        $query = $this->buildQuery($request);
        $logs = $query->limit(5000)->get();

        $filename = 'system_health_logs_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Check', 'Status', 'Message', 'Created At', 'Meta']);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->id,
                    $log->check_name,
                    $log->status,
                    $log->message,
                    $log->created_at ? date('Y-m-d H:i:s', $log->created_at) : '',
                    $log->meta ? json_encode($log->meta) : '',
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

        $query = $this->buildQuery($request);
        $logs = $query->limit(5000)->get();
        $filename = 'system_health_logs_' . date('Y-m-d_His') . '.json';

        return response()->streamDownload(
            function () use ($logs) {
                echo json_encode(['data' => $logs->toArray()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            },
            $filename,
            ['Content-Type' => 'application/json', 'Content-Disposition' => 'attachment; filename="' . $filename . '"']
        );
    }

    public function indexApi(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $perPage = min((int) $request->get('per_page', 15), 100);
        $query = $this->buildQuery($request);
        $healthLogs = $query->paginate($perPage);

        return response()->json([
            'data' => $healthLogs->items(),
            'meta' => [
                'current_page' => $healthLogs->currentPage(),
                'last_page' => $healthLogs->lastPage(),
                'per_page' => $healthLogs->perPage(),
                'total' => $healthLogs->total(),
            ],
        ]);
    }

    public function showApi($id)
    {
        $this->authorize('admin_general_dashboard_show');

        $healthLog = HealthLog::findOrFail($id);

        return response()->json(['data' => $healthLog]);
    }

    protected function buildQuery(Request $request)
    {
        $query = HealthLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('check_name')) {
            $query->where('check_name', 'like', '%' . $request->get('check_name') . '%');
        }
        if ($request->filled('date_from')) {
            $ts = strtotime($request->get('date_from') . ' 00:00:00');
            if ($ts) {
                $query->where('created_at', '>=', $ts);
            }
        }
        if ($request->filled('date_to')) {
            $ts = strtotime($request->get('date_to') . ' 23:59:59');
            if ($ts) {
                $query->where('created_at', '<=', $ts);
            }
        }

        return $query;
    }

    protected function getStats(Request $request): array
    {
        $base = HealthLog::query();
        $this->applyDateFilter($base, $request);

        $total = (clone $base)->count();
        $ok = (clone $base)->where('status', HealthLog::STATUS_OK)->count();
        $warning = (clone $base)->where('status', HealthLog::STATUS_WARNING)->count();
        $failed = (clone $base)->where('status', HealthLog::STATUS_FAILED)->count();

        $last24h = HealthLog::where('created_at', '>=', time() - 86400)->count();
        $latest = HealthLog::orderBy('created_at', 'desc')->first();

        return [
            'total' => $total,
            'ok' => $ok,
            'warning' => $warning,
            'failed' => $failed,
            'last_24h' => $last24h,
            'latest_at' => $latest ? $latest->created_at : null,
        ];
    }

    protected function getChartData(Request $request): array
    {
        $days = 14;
        $end = time();
        $start = $end - ($days * 86400);
        $query = HealthLog::where('created_at', '>=', $start)->where('created_at', '<=', $end);
        $this->applyDateFilter($query, $request);

        $logs = $query->get();
        $labels = [];
        $okData = [];
        $warningData = [];
        $failedData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $dayStart = strtotime('midnight', $end - $i * 86400);
            $dayEnd = $dayStart + 86400 - 1;
            $labels[] = date('M j', $dayStart);
            $dayLogs = $logs->filter(function ($log) use ($dayStart, $dayEnd) {
                return $log->created_at >= $dayStart && $log->created_at <= $dayEnd;
            });
            $okData[] = $dayLogs->where('status', HealthLog::STATUS_OK)->count();
            $warningData[] = $dayLogs->where('status', HealthLog::STATUS_WARNING)->count();
            $failedData[] = $dayLogs->where('status', HealthLog::STATUS_FAILED)->count();
        }

        $statusCounts = [
            'ok' => $logs->where('status', HealthLog::STATUS_OK)->count(),
            'warning' => $logs->where('status', HealthLog::STATUS_WARNING)->count(),
            'failed' => $logs->where('status', HealthLog::STATUS_FAILED)->count(),
        ];

        return [
            'labels' => $labels,
            'ok' => $okData,
            'warning' => $warningData,
            'failed' => $failedData,
            'donut' => $statusCounts,
        ];
    }

    protected function applyDateFilter($query, Request $request): void
    {
        if ($request->filled('date_from')) {
            $ts = strtotime($request->get('date_from') . ' 00:00:00');
            if ($ts) {
                $query->where('created_at', '>=', $ts);
            }
        }
        if ($request->filled('date_to')) {
            $ts = strtotime($request->get('date_to') . ' 23:59:59');
            if ($ts) {
                $query->where('created_at', '<=', $ts);
            }
        }
    }
}
