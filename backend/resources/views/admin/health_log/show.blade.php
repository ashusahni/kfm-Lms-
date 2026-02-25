@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }} #{{ $healthLog->id }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/health-log">{{ trans('admin/main.health_log') ?? 'Health Log' }}</a></div>
                <div class="breadcrumb-item">#{{ $healthLog->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered font-14">
                        <tr>
                            <th width="160">ID</th>
                            <td>{{ $healthLog->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('admin/main.check_name') ?? 'Check name' }}</th>
                            <td>{{ $healthLog->check_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('admin/main.status') }}</th>
                            <td>
                                @if($healthLog->status === 'ok')
                                    <span class="badge badge-success">OK</span>
                                @elseif($healthLog->status === 'warning')
                                    <span class="badge badge-warning">Warning</span>
                                @else
                                    <span class="badge badge-danger">Failed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ trans('site.message') }}</th>
                            <td>{{ $healthLog->message ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('admin/main.created_at') }}</th>
                            <td>{{ $healthLog->created_at ? dateTimeFormat($healthLog->created_at, 'j M Y H:i') : '—' }}</td>
                        </tr>
                        @if(!empty($healthLog->meta))
                            <tr>
                                <th>Meta (JSON)</th>
                                <td><pre class="mb-0 p-3 bg-light rounded font-12">{{ json_encode($healthLog->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></td>
                            </tr>
                        @endif
                    </table>
                    <div class="mt-3">
                        <a href="{{ getAdminPanelUrl() }}/health-log" class="btn btn-secondary">{{ trans('public.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
