@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/health-log">{{ trans('admin/main.health_log') ?? 'Health Log' }}</a></div>
                <div class="breadcrumb-item">#{{ $log->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ trans('admin/main.log') ?? 'Log' }} #{{ $log->id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ trans('admin/main.student') ?? 'Student' }}:</strong> {{ optional($log->user)->full_name ?? '—' }} (ID: {{ $log->user_id }})</p>
                            <p><strong>{{ trans('admin/main.email') ?? 'Email' }}:</strong> {{ optional($log->user)->email ?? '—' }}</p>
                            <p><strong>{{ trans('admin/main.course') ?? 'Course' }}:</strong> {{ optional($log->webinar)->title ?? '—' }} @if($log->webinar_id)(ID: {{ $log->webinar_id }})@endif</p>
                            <p><strong>{{ trans('admin/main.log_date') ?? 'Log date' }}:</strong> {{ $log->log_date ? (\Carbon\Carbon::parse($log->log_date)->format('Y-m-d')) : '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ trans('admin/main.water') ?? 'Water' }} (ml):</strong> {{ $log->water_ml ?? '—' }}</p>
                            <p><strong>{{ trans('admin/main.calories') ?? 'Calories' }}:</strong> {{ $log->calories ?? '—' }}</p>
                            <p><strong>{{ trans('admin/main.protein') ?? 'Protein' }} / {{ trans('admin/main.carbs') ?? 'Carbs' }} / {{ trans('admin/main.fat') ?? 'Fat' }} (g):</strong> {{ $log->protein ?? '—' }} / {{ $log->carbs ?? '—' }} / {{ $log->fat ?? '—' }}</p>
                            <p><strong>{{ trans('admin/main.activity') ?? 'Activity' }} (min):</strong> {{ $log->activity_minutes ?? '—' }}</p>
                            <p><strong>{{ trans('admin/main.adherence') ?? 'Adherence' }} %:</strong> {{ $log->adherence_score !== null ? $log->adherence_score . '%' : '—' }}</p>
                        </div>
                    </div>
                    @if($log->activity_notes)
                        <p><strong>{{ trans('admin/main.activity_notes') ?? 'Activity notes' }}:</strong><br>{{ $log->activity_notes }}</p>
                    @endif
                    @if($log->medicines)
                        <p><strong>{{ trans('admin/main.medicines_supplements') ?? 'Medicines / supplements' }}:</strong><br>{{ $log->medicines }}</p>
                    @endif
                    @if(!empty($log->meals) && is_array($log->meals))
                        <p><strong>{{ trans('admin/main.meals') ?? 'Meals' }}:</strong></p>
                        <pre class="bg-light p-3 rounded font-12">{{ json_encode($log->meals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                    @if(!empty($log->custom_data) && is_array($log->custom_data))
                        <p><strong>{{ trans('admin/main.custom_data') ?? 'Custom data' }}:</strong></p>
                        <pre class="bg-light p-3 rounded font-12">{{ json_encode($log->custom_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ getAdminPanelUrl() }}/health-log?{{ http_build_query(request()->query()) }}" class="btn btn-secondary">{{ trans('public.back') ?? 'Back' }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
