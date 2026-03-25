@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/webinars">{{ trans('admin/main.webinars') ?? 'Courses' }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches">{{ $webinar->title }} — {{ trans('admin/main.course_batches') ?? 'Batches' }}</a></div>
                <div class="breadcrumb-item">{{ $batch->name }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $batch->name }} (ID: {{ $batch->id }})</h4>
                </div>
                <form method="post" action="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches/{{ $batch->id }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('admin/main.name') ?? 'Name' }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $batch->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('admin/main.code') ?? 'Code' }}</label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $batch->code) }}" placeholder="e.g. JAN2025">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('admin/main.start_date') ?? 'Start date' }}</label>
                                    <input type="number" name="start_date" class="form-control" value="{{ old('start_date', $batch->start_date) }}" placeholder="Unix timestamp (optional)">
                                    @if($webinar->isChallengeCourse())
                                        <small class="text-info d-block mt-1">{{ trans('admin/main.challenge_batch_hint') ?? 'For ' . $webinar->getChallengeDurationDays() . '-day challenge, end date is auto-set from start + ' . $webinar->getChallengeDurationDays() . ' days if left empty.' }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('admin/main.end_date') ?? 'End date' }}</label>
                                    <input type="number" name="end_date" class="form-control" value="{{ old('end_date', $batch->end_date) }}" placeholder="Unix timestamp (optional)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('admin/main.capacity') ?? 'Capacity' }}</label>
                                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $batch->capacity) }}" min="0" placeholder="{{ trans('admin/main.unlimited_if_empty') ?? 'Unlimited if empty' }}">
                                    <small class="text-muted">{{ trans('admin/main.enrolled_count') ?? 'Enrolled' }}: {{ $batch->enrolled_count }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('admin/main.status') ?? 'Status' }}</label>
                                    <select name="status" class="form-control">
                                        @foreach(\App\Models\CourseBatch::$statuses as $s)
                                            <option value="{{ $s }}" {{ old('status', $batch->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('admin/main.sort_order') ?? 'Sort order' }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $batch->sort_order) }}" min="0">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('public.save') ?? 'Save' }}</button>
                        <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches" class="btn btn-secondary">{{ trans('public.cancel') ?? 'Cancel' }}</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
