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
            <div class="card">
                <div class="card-header">
                    <h4>{{ trans('admin/main.course_health_log_settings') ?? 'Course health log settings' }}</h4>
                    <div class="card-header-action">
                        <form method="get" action="{{ getAdminPanelUrl() }}/course-health-log-settings" class="form-inline">
                            <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="{{ trans('admin/main.search_course') ?? 'Search course...' }}" value="{{ request('q') }}">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ trans('admin/main.title') ?? 'Course' }}</th>
                                    <th class="text-center">{{ trans('admin/main.health_log_enabled') ?? 'Health log' }}</th>
                                    <th class="text-center" width="120">{{ trans('public.controls') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($webinars as $w)
                                    <tr>
                                        <td>{{ $w->id }}</td>
                                        <td>{{ $w->title }}</td>
                                        <td class="text-center">
                                            @if(in_array($w->id, $settingIds ?? []))
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> {{ trans('admin/main.configured') ?? 'Configured' }}</span>
                                            @else
                                                <span class="badge badge-secondary">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ getAdminPanelUrl() }}/course-health-log-settings/{{ $w->id }}/edit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog"></i> {{ trans('admin/main.settings') ?? 'Settings' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">{{ trans('admin/main.no_courses') ?? 'No courses found.' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($webinars->hasPages())
                <div class="card-footer text-center">
                    {{ $webinars->appends(request()->input())->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
