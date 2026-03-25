@extends(getTemplate() . '.panel.layouts.panel_layout')

@section('content')
    <section>
        <h2 class="section-title">{{ trans('admin/main.course_batches') ?? 'Course batches' }} — {{ $webinar->title }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-20">
                <a href="/panel/webinars/{{ $webinar->id }}/batches/create" class="btn btn-primary">
                    <i data-feather="plus" class="mr-5" width="16"></i> {{ trans('admin/main.create_batch') ?? 'Create batch' }}
                </a>
                <form method="get" action="/panel/webinars/{{ $webinar->id }}/batches" class="form-inline">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">{{ trans('admin/main.all_statuses') ?? 'All statuses' }}</option>
                        @foreach(\App\Models\CourseBatch::$statuses as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table custom-table text-center font-14">
                    <thead>
                        <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">{{ trans('admin/main.name') ?? 'Name' }}</th>
                            <th>{{ trans('admin/main.code') ?? 'Code' }}</th>
                            <th>{{ trans('admin/main.start_date') ?? 'Start' }}</th>
                            <th>{{ trans('admin/main.end_date') ?? 'End' }}</th>
                            <th class="text-center">{{ trans('admin/main.capacity') ?? 'Capacity' }}</th>
                            <th class="text-center">{{ trans('admin/main.enrolled') ?? 'Enrolled' }}</th>
                            <th>{{ trans('admin/main.status') ?? 'Status' }}</th>
                            <th width="140">{{ trans('public.controls') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td class="text-left">{{ $batch->id }}</td>
                                <td class="text-left">{{ $batch->name }}</td>
                                <td>{{ $batch->code ?? '—' }}</td>
                                <td>{{ $batch->start_date ? dateTimeFormat($batch->start_date, 'j M Y') : '—' }}</td>
                                <td>{{ $batch->end_date ? dateTimeFormat($batch->end_date, 'j M Y') : '—' }}</td>
                                <td class="text-center">{{ $batch->capacity ?? '∞' }}</td>
                                <td class="text-center">{{ $batch->enrolled_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $batch->status === 'open' ? 'success' : ($batch->status === 'draft' ? 'secondary' : 'warning') }}">{{ ucfirst($batch->status) }}</span>
                                </td>
                                <td>
                                    <a href="/panel/webinars/{{ $webinar->id }}/batches/{{ $batch->id }}/edit" class="btn btn-sm btn-outline-primary"><i data-feather="edit-2" width="14"></i></a>
                                    @if($batch->sales()->count() == 0)
                                        <a href="/panel/webinars/{{ $webinar->id }}/batches/{{ $batch->id }}/delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ trans('admin/main.confirm_delete_batch') ?? 'Delete this batch?' }}');"><i data-feather="trash-2" width="14"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-gray">{{ trans('admin/main.no_batches') ?? 'No batches yet. Create one to offer this course in batches.' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($batches->hasPages())
                <div class="mt-20 d-flex justify-content-center">
                    {{ $batches->appends(request()->input())->links() }}
                </div>
            @endif
        </div>

        <div class="mt-20">
            <a href="/panel/webinars" class="btn btn-secondary">{{ trans('public.back') ?? 'Back to courses' }}</a>
        </div>
    </section>
@endsection
