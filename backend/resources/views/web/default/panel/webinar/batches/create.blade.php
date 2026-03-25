@extends(getTemplate() . '.panel.layouts.panel_layout')

@section('content')
    <section>
        <h2 class="section-title">{{ trans('admin/main.create_batch') ?? 'Create batch' }} — {{ $webinar->title }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form method="post" action="/panel/webinars/{{ $webinar->id }}/batches">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.name') ?? 'Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.code') ?? 'Code' }}</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="e.g. JAN2025">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.start_date') ?? 'Start date' }}</label>
                            <input type="number" name="start_date" class="form-control" value="{{ old('start_date') }}" placeholder="Unix timestamp (optional)">
                            <small class="text-muted">{{ trans('admin/main.unix_timestamp_optional') ?? 'Leave empty for no start limit' }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.end_date') ?? 'End date' }}</label>
                            <input type="number" name="end_date" class="form-control" value="{{ old('end_date') }}" placeholder="Unix timestamp (optional)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.capacity') ?? 'Capacity' }}</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" min="0" placeholder="{{ trans('admin/main.unlimited_if_empty') ?? 'Unlimited if empty' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.status') ?? 'Status' }}</label>
                            <select name="status" class="form-control">
                                @foreach(\App\Models\CourseBatch::$statuses as $s)
                                    <option value="{{ $s }}" {{ old('status', 'draft') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.sort_order') ?? 'Sort order' }}</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                </div>
                <div class="mt-25 d-flex">
                    <button type="submit" class="btn btn-primary">{{ trans('public.save') ?? 'Save' }}</button>
                    <a href="/panel/webinars/{{ $webinar->id }}/batches" class="btn btn-secondary ml-10">{{ trans('public.cancel') ?? 'Cancel' }}</a>
                </div>
            </form>
        </div>
    </section>
@endsection
