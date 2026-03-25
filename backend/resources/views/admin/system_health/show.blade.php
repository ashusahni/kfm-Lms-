@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/system-health">{{ trans('admin/main.system_health') ?? 'System health' }}</a></div>
                <div class="breadcrumb-item">#{{ $healthLog->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ trans('admin/main.health_log_detail') ?? 'Log detail' }} #{{ $healthLog->id }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>{{ trans('admin/main.check_name') ?? 'Check' }}:</strong> {{ $healthLog->check_name }}</p>
                    <p><strong>{{ trans('admin/main.status') ?? 'Status' }}:</strong>
                        @if($healthLog->status === 'ok')
                            <span class="badge badge-success">OK</span>
                        @elseif($healthLog->status === 'warning')
                            <span class="badge badge-warning">Warning</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </p>
                    <p><strong>{{ trans('site.message') }}:</strong><br>{{ $healthLog->message ?? '—' }}</p>
                    @if(!empty($healthLog->meta))
                        <p><strong>Meta:</strong></p>
                        <pre class="bg-light p-3 rounded font-12">{{ json_encode($healthLog->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                    <p class="text-muted font-14 mb-0"><strong>{{ trans('admin/main.created_at') ?? 'Created' }}:</strong> {{ $healthLog->created_at ? dateTimeFormat($healthLog->created_at, 'j M Y H:i:s') : '—' }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ getAdminPanelUrl() }}/system-health" class="btn btn-secondary">{{ trans('public.back') ?? 'Back' }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
