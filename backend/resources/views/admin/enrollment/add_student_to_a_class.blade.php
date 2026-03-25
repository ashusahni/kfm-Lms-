@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/enrollments/history">{{ trans('public.history') }}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ getAdminPanelUrl() }}/enrollments/store" method="post">
                                @csrf

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.student') }} <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-control search-user-select2" data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.student') }}" data-search-option="just_student_role" required>
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.class') }} ({{ trans('admin/main.course') }})</label>
                                    <select name="webinar_id" id="webinar_id" class="form-control search-webinar-select2" data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.class') }}">
                                        <option value=""></option>
                                    </select>
                                    <small class="form-text text-muted">{{ trans('public.choose_one_of_the_following') ?? 'Choose either a course or a bundle.' }}</small>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.bundle') }}</label>
                                    <select name="bundle_id" id="bundle_id" class="form-control search-bundle-select2" data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.bundle') }}">
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                    <a href="{{ getAdminPanelUrl() }}/enrollments/history" class="btn btn-secondary">{{ trans('admin/main.cancel') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        var webinarId = document.getElementById('webinar_id').value;
        var bundleId = document.getElementById('bundle_id').value;
        if (!webinarId && !bundleId) {
            e.preventDefault();
            alert('Please select a course or a bundle.');
            return false;
        }
        if (webinarId && bundleId) {
            e.preventDefault();
            alert('Please select only one: either a course or a bundle.');
            return false;
        }
    });
</script>
@endpush
