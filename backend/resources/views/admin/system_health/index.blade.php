@extends('admin.layouts.app')

@push('libraries_top')
    <link rel="stylesheet" href="/assets/default/vendors/chartjs/chart.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/health-log">{{ trans('admin/main.health_log') ?? 'Health Log' }}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ getAdminPanelUrl() }}/health-log" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> {{ trans('admin/main.back_to_health_log') ?? 'Back to Health Log' }}</a>
                </div>
            </div>

            {{-- Summary stats --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-clipboard-list"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.health_log_total') ?? 'Total checks' }}</h4></div>
                            <div class="card-body">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.health_status_ok') ?? 'OK' }}</h4></div>
                            <div class="card-body">{{ $stats['ok'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.health_status_warning') ?? 'Warning' }}</h4></div>
                            <div class="card-body">{{ $stats['warning'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.health_status_failed') ?? 'Failed' }}</h4></div>
                            <div class="card-body">{{ $stats['failed'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header flex-wrap">
                            <h4 class="mb-0">{{ trans('admin/main.health_log_actions') ?? 'Actions & APIs' }}</h4>
                            <div class="card-header-action ml-auto">
                                <form method="post" action="{{ getAdminPanelUrl() }}/system-health/run-check" class="d-inline" id="health-log-run-form">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg btn-icon icon-left" id="btn-run-check">
                                        <i class="fas fa-play"></i> {{ trans('admin/main.run_health_check') ?? 'Run health check' }}
                                    </button>
                                </form>
                                <div class="btn-group ml-2">
                                    <a href="{{ getAdminPanelUrl() }}/system-health/export/csv?{{ http_build_query(request()->query()) }}" class="btn btn-outline-secondary">CSV</a>
                                    <a href="{{ getAdminPanelUrl() }}/system-health/export/json?{{ http_build_query(request()->query()) }}" class="btn btn-outline-secondary">JSON</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            @if(!empty($stats['latest_at']))
                                <p class="text-muted mb-0 font-14">
                                    {{ trans('admin/main.last_check_at') ?? 'Last check' }}: {{ dateTimeFormat($stats['latest_at'], 'j M Y H:i') }}
                                    &nbsp;|&nbsp; {{ trans('admin/main.checks_last_24h') ?? 'Checks in last 24h' }}: {{ $stats['last_24h'] ?? 0 }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($chartData) && (($chartData['donut']['ok'] ?? 0) + ($chartData['donut']['warning'] ?? 0) + ($chartData['donut']['failed'] ?? 0)) > 0)
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header"><h4>{{ trans('admin/main.health_status_distribution') ?? 'Status distribution' }}</h4></div>
                        <div class="card-body"><canvas id="healthLogDonutChart" height="200"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header"><h4>{{ trans('admin/main.health_trend_14_days') ?? 'Last 14 days' }}</h4></div>
                        <div class="card-body"><canvas id="healthLogTrendChart" height="200"></canvas></div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header"><h4>{{ trans('admin/main.health_log_list') ?? 'Health log entries' }}</h4></div>
                <div class="card-body">
                    <form method="get" action="{{ getAdminPanelUrl() }}/system-health" class="form-inline flex-wrap mb-4">
                        <input type="date" name="date_from" class="form-control form-control-sm mr-2 mb-2" value="{{ request('date_from') }}">
                        <input type="date" name="date_to" class="form-control form-control-sm mr-2 mb-2" value="{{ request('date_to') }}">
                        <select name="status" class="form-control form-control-sm mr-2 mb-2">
                            <option value="">{{ trans('admin/main.all_statuses') ?? 'All' }}</option>
                            <option value="ok" {{ request('status') == 'ok' ? 'selected' : '' }}>OK</option>
                            <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <input type="text" name="check_name" class="form-control form-control-sm mr-2 mb-2" placeholder="{{ trans('admin/main.check_name') ?? 'Check name' }}" value="{{ request('check_name') }}">
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fas fa-filter"></i> {{ trans('admin/main.filter') }}</button>
                        @if(request()->hasAny(['date_from','date_to','status','check_name']))
                            <a href="{{ getAdminPanelUrl() }}/system-health" class="btn btn-sm btn-outline-secondary ml-2 mb-2">{{ trans('public.clear') ?? 'Clear' }}</a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ trans('admin/main.check_name') ?? 'Check' }}</th>
                                    <th class="text-center">{{ trans('admin/main.status') }}</th>
                                    <th>{{ trans('site.message') }}</th>
                                    <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                    <th class="text-center" width="80">{{ trans('public.controls') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($healthLogs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->check_name }}</td>
                                        <td class="text-center">
                                            @if($log->status === 'ok')
                                                <span class="badge badge-success">OK</span>
                                            @elseif($log->status === 'warning')
                                                <span class="badge badge-warning">Warning</span>
                                            @else
                                                <span class="badge badge-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($log->message, 60) }}</td>
                                        <td class="text-center">{{ $log->created_at ? dateTimeFormat($log->created_at, 'j M Y H:i') : '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ getAdminPanelUrl() }}/system-health/{{ $log->id }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            {{ trans('admin/main.health_log_no_entries') ?? 'No entries yet.' }}
                                            <form method="post" action="{{ getAdminPanelUrl() }}/system-health/run-check" class="d-inline mt-2">@csrf
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-play mr-1"></i> {{ trans('admin/main.run_health_check') ?? 'Run check' }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($healthLogs->hasPages())
                <div class="card-footer text-center">{{ $healthLogs->appends(request()->input())->links() }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/chartjs/chart.min.js"></script>
    <script>
        (function() {
            var runForm = document.getElementById('health-log-run-form');
            if (runForm) {
                runForm.addEventListener('submit', function() {
                    var btn = document.getElementById('btn-run-check');
                    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...'; }
                });
            }
            @if(!empty($chartData) && (($chartData['donut']['ok'] ?? 0) + ($chartData['donut']['warning'] ?? 0) + ($chartData['donut']['failed'] ?? 0)) > 0)
            var donutCtx = document.getElementById('healthLogDonutChart');
            if (donutCtx) {
                new Chart(donutCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['OK', 'Warning', 'Failed'],
                        datasets: [{
                            data: [{{ $chartData['donut']['ok'] ?? 0 }}, {{ $chartData['donut']['warning'] ?? 0 }}, {{ $chartData['donut']['failed'] ?? 0 }}],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, legend: { position: 'bottom' } }
                });
            }
            var trendCtx = document.getElementById('healthLogTrendChart');
            if (trendCtx) {
                new Chart(trendCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($chartData['labels'] ?? []),
                        datasets: [
                            { label: 'OK', data: @json($chartData['ok'] ?? []), borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)', fill: true, tension: 0.3 },
                            { label: 'Warning', data: @json($chartData['warning'] ?? []), borderColor: '#ffc107', backgroundColor: 'rgba(255,193,7,0.1)', fill: true, tension: 0.3 },
                            { label: 'Failed', data: @json($chartData['failed'] ?? []), borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', fill: true, tension: 0.3 }
                        ]
                    },
                    options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true } }] }, legend: { position: 'bottom' } }
                });
            }
            @endif
        })();
    </script>
@endpush
