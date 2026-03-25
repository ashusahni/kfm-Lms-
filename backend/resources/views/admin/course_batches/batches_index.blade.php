@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.course_batches') ?? 'Course batches' }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ trans('admin/main.course_batches') ?? 'Course batches' }}</h4>
                    <div class="card-header-action">
                        <form method="get" action="{{ getAdminPanelUrl() }}/batches" class="form-inline d-inline">
                            <input type="text" name="q" class="form-control form-control-sm mr-2" value="{{ request('q') }}" placeholder="{{ trans('admin/main.search_courses') ?? 'Search courses' }}">
                            <select name="type" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="">{{ trans('admin/main.all_types') ?? 'All types' }}</option>
                                <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>{{ trans('admin/main.courses') }}</option>
                                <option value="webinar" {{ request('type') == 'webinar' ? 'selected' : '' }}>{{ trans('admin/main.live_classes') }}</option>
                                <option value="text_lesson" {{ request('type') == 'text_lesson' ? 'selected' : '' }}>{{ trans('admin/main.text_courses') }}</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">{{ trans('admin/main.search') ?? 'Search' }}</button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin/main.title') ?? 'Course' }}</th>
                                    <th>{{ trans('admin/main.type') ?? 'Type' }}</th>
                                    <th class="text-center">{{ trans('admin/main.batches_count') ?? 'Batches' }}</th>
                                    <th width="180">{{ trans('public.controls') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($webinars as $webinar)
                                    <tr>
                                        <td>{{ $webinar->id }}</td>
                                        <td>{{ $webinar->title }}</td>
                                        <td>{{ trans('webinars.' . $webinar->type) ?? $webinar->type }}</td>
                                        <td class="text-center">{{ $webinar->batches_count ?? 0 }}</td>
                                        <td>
                                            <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/batches" class="btn btn-sm btn-primary">
                                                <i class="fas fa-list"></i> {{ trans('admin/main.manage_batches') ?? 'Manage batches' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">{{ trans('admin/main.no_courses_found') ?? 'No courses found.' }}</td>
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
