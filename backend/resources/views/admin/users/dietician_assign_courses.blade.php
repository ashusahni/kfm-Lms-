@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/dieticians">{{ trans('admin/main.dietitians') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.assign_courses') ?? 'Assign courses' }}</div>
            </div>
        </div>

        @if(!empty(session('toast')))
            @php $toast = session('toast'); @endphp
            <div class="alert alert-{{ $toast['status'] ?? 'success' }} my-25">
                <strong>{{ $toast['title'] ?? '' }}</strong> {{ $toast['msg'] ?? '' }}
            </div>
        @endif

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.dietitian') ?? 'Dietitian' }}</label>
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="{{ $user->getAvatar() }}" alt="{{ $user->full_name }}">
                                    </figure>
                                    <div>
                                        <div class="font-weight-bold">{{ $user->full_name }}</div>
                                        @if($user->email)
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <form action="{{ getAdminPanelUrl() }}/dieticians/{{ $user->id }}/assign-courses" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.courses') ?? 'Courses (programs)' }} <span class="text-muted">({{ trans('admin/main.select_multiple') ?? 'Select one or more' }})</span></label>
                                    <select name="webinar_ids[]" id="webinar_ids" class="form-control select2-multiple" multiple data-placeholder="{{ trans('admin/main.select_courses') ?? 'Select courses to assign' }}">
                                        @foreach($webinars as $webinar)
                                            <option value="{{ $webinar->id }}" @if(in_array($webinar->id, $assignedIds)) selected @endif>
                                                {{ $webinar->title }} (ID: {{ $webinar->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ trans('admin/main.assign_courses_hint') ?? 'Assigned courses will appear in the dietician panel. The dietician can manage batches and view students for these courses only.' }}</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.save') ?? 'Save' }}</button>
                                    <a href="{{ getAdminPanelUrl() }}/dieticians" class="btn btn-secondary">{{ trans('admin/main.cancel') ?? 'Cancel' }}</a>
                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $user->id }}/edit" class="btn btn-outline-secondary">{{ trans('admin/main.edit') }} {{ trans('admin/main.user') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.assigned_courses') ?? 'Currently assigned' }} ({{ count($assignedIds) }})</h4>
                        </div>
                        <div class="card-body">
                            @if(count($assignedIds) > 0)
                                <ul class="list-unstyled mb-0">
                                    @foreach($webinars->whereIn('id', $assignedIds) as $w)
                                        <li class="mb-2">{{ $w->title }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">{{ trans('admin/main.no_courses_assigned') ?? 'No courses assigned yet. Use the form to assign courses.' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
<script src="/assets/default/vendors/select2/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#webinar_ids').select2({
            placeholder: $('#webinar_ids').data('placeholder'),
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
