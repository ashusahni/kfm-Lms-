@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            {{-- Stats --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-clipboard-list"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.total_logs') ?? 'Total logs' }}</h4></div>
                            <div class="card-body">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-book"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.logs_with_course') ?? 'With course' }}</h4></div>
                            <div class="card-body">{{ $stats['with_course'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-users"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.unique_students') ?? 'Unique students' }}</h4></div>
                            <div class="card-body">{{ $stats['unique_users'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-percentage"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>{{ trans('admin/main.avg_adherence') ?? 'Avg adherence %' }}</h4></div>
                            <div class="card-body">{{ $stats['avg_adherence'] ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>{{ trans('admin/main.student_health_logs') ?? 'Student health logs' }}</h4>
                    <div class="card-header-action">
                        <a href="{{ getAdminPanelUrl() }}/student-health-logs/export/csv?{{ http_build_query(request()->query()) }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-csv"></i> {{ trans('admin/main.export_csv') ?? 'Export CSV' }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ getAdminPanelUrl() }}/student-health-logs" class="form-inline flex-wrap mb-4">
                        <select name="webinar_id" class="form-control form-control-sm mr-2 mb-2">
                            <option value="">{{ trans('admin/main.all_courses') ?? 'All courses' }}</option>
                            @foreach($coursesWithLogs as $c)
                                <option value="{{ $c->id }}" {{ request('webinar_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="user_id" class="form-control form-control-sm mr-2 mb-2" placeholder="User ID" value="{{ request('user_id') }}" style="width:100px">
                        <input type="date" name="from_date" class="form-control form-control-sm mr-2 mb-2" value="{{ request('from_date') }}">
                        <input type="date" name="to_date" class="form-control form-control-sm mr-2 mb-2" value="{{ request('to_date') }}">
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fas fa-filter"></i> {{ trans('admin/main.filter') }}</button>
                        @if(request()->hasAny(['webinar_id','user_id','from_date','to_date']))
                            <a href="{{ getAdminPanelUrl() }}/student-health-logs" class="btn btn-sm btn-outline-secondary ml-2 mb-2">{{ trans('public.clear') ?? 'Clear' }}</a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ trans('admin/main.student') ?? 'Student' }}</th>
                                    <th>{{ trans('admin/main.course') ?? 'Course' }}</th>
                                    <th>{{ trans('admin/main.log_date') ?? 'Date' }}</th>
                                    <th class="text-center">{{ trans('admin/main.water') ?? 'Water' }}</th>
                                    <th class="text-center">{{ trans('admin/main.calories') ?? 'Cal' }}</th>
                                    <th class="text-center">{{ trans('admin/main.activity') ?? 'Activity' }}</th>
                                    <th class="text-center">{{ trans('admin/main.adherence') ?? 'Adherence' }}</th>
                                    <th class="text-center" width="80">{{ trans('public.controls') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->user->full_name ?? $log->user_id }} <small class="text-muted">#{{ $log->user_id }}</small></td>
                                        <td>{{ $log->webinar->title ?? '—' }}</td>
                                        <td>{{ $log->log_date ? $log->log_date->format('Y-m-d') : '—' }}</td>
                                        <td class="text-center">{{ $log->water_ml ?? '—' }}</td>
                                        <td class="text-center">{{ $log->calories ?? '—' }}</td>
                                        <td class="text-center">{{ $log->activity_minutes ?? '—' }} min</td>
                                        <td class="text-center">{{ $log->adherence_score !== null ? $log->adherence_score . '%' : '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ getAdminPanelUrl() }}/student-health-logs/{{ $log->id }}" class="btn btn-sm btn-outline-primary" title="{{ trans('admin/main.show') }}"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">{{ trans('admin/main.no_student_health_logs') ?? 'No student health logs yet.' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer text-center">
                    {{ $logs->appends(request()->input())->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
