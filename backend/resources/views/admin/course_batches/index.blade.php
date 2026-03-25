@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/webinars">{{ trans('admin/main.webinars') ?? 'Courses' }}</a></div>
                <div class="breadcrumb-item">{{ $webinar->title }}</div>
                <div class="breadcrumb-item">{{ trans('admin/main.course_batches') ?? 'Batches' }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $webinar->title }} (ID: {{ $webinar->id }}) — {{ trans('admin/main.course_batches') ?? 'Batches' }}</h4>
                    <div class="card-header-action">
                        <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ trans('admin/main.create_batch') ?? 'Create batch' }}
                        </a>
                        <form method="get" action="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches" class="form-inline d-inline ml-2">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">{{ trans('admin/main.all_statuses') ?? 'All statuses' }}</option>
                                @foreach(\App\Models\CourseBatch::$statuses as $s)
                                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin/main.name') ?? 'Name' }}</th>
                                    <th>{{ trans('admin/main.code') ?? 'Code' }}</th>
                                    <th>{{ trans('admin/main.start_date') ?? 'Start' }}</th>
                                    <th>{{ trans('admin/main.end_date') ?? 'End' }}</th>
                                    <th class="text-center">{{ trans('admin/main.capacity') ?? 'Capacity' }}</th>
                                    <th class="text-center">{{ trans('admin/main.enrolled') ?? 'Enrolled' }}</th>
                                    <th>{{ trans('admin/main.status') ?? 'Status' }}</th>
                                    <th width="180">{{ trans('public.controls') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
                                    <tr>
                                        <td>{{ $batch->id }}</td>
                                        <td>{{ $batch->name }}</td>
                                        <td>{{ $batch->code ?? '—' }}</td>
                                        <td>{{ $batch->start_date ? dateTimeFormat($batch->start_date, 'j M Y') : '—' }}</td>
                                        <td>{{ $batch->end_date ? dateTimeFormat($batch->end_date, 'j M Y') : '—' }}</td>
                                        <td class="text-center">{{ $batch->capacity ?? '∞' }}</td>
                                        <td class="text-center">{{ $batch->enrolled_count }}</td>
                                        <td>
                                            <span class="badge badge-{{ $batch->status === 'open' ? 'success' : ($batch->status === 'draft' ? 'secondary' : 'warning') }}">{{ ucfirst($batch->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches/{{ $batch->id }}/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                            @if($batch->sales()->count() == 0)
                                                <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches/{{ $batch->id }}/delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ trans('admin/main.confirm_delete_batch') ?? 'Delete this batch?' }}');"><i class="fas fa-trash"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">{{ trans('admin/main.no_batches') ?? 'No batches yet. Create one to offer this course in batches.' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($batches->hasPages())
                <div class="card-footer text-center">
                    {{ $batches->appends(request()->input())->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
