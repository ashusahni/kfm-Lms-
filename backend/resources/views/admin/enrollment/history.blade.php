@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.search') }}</label>
                                    <input type="text" class="form-control" name="item_title" value="{{ request()->get('item_title') }}" placeholder="{{ trans('admin/main.class') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                    <input type="date" class="form-control" name="from" value="{{ request()->get('from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                    <input type="date" class="form-control" name="to" value="{{ request()->get('to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="">{{ trans('admin/main.all_status') }}</option>
                                        <option value="success" @if(request()->get('status') == 'success') selected @endif>{{ trans('admin/main.success') }}</option>
                                        <option value="refund" @if(request()->get('status') == 'refund') selected @endif>{{ trans('admin/main.refund') }}</option>
                                        <option value="blocked" @if(request()->get('status') == 'blocked') selected @endif>{{ trans('update.access_blocked') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.class') }}</label>
                                    <select name="webinar_ids[]" multiple="multiple" class="form-control search-webinar-select2" data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.class') }}">
                                        @if(!empty($webinars) && $webinars->count() > 0)
                                            @foreach($webinars as $webinar)
                                                <option value="{{ $webinar->id }}" selected>{{ $webinar->title ?? '#' . $webinar->id }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.instructor') }}</label>
                                    <select name="teacher_ids[]" multiple="multiple" data-search-option="just_teacher_role" class="form-control search-user-select2" data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.instructor') }}">
                                        @if(!empty($teachers) && $teachers->count() > 0)
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" selected>{{ $teacher->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.student') }}</label>
                                    <select name="student_ids[]" multiple="multiple" data-search-option="just_student_role" class="form-control search-user-select2" data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.student') }}">
                                        @if(!empty($students) && $students->count() > 0)
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" selected>{{ $student->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary w-100" value="{{ trans('admin/main.show_results') }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_sales_export')
                                <a href="{{ getAdminPanelUrl() }}/enrollments/export?{{ http_build_query(request()->query()) }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-left">{{ trans('admin/main.student') }}</th>
                                        <th class="text-left">{{ trans('admin/main.instructor') }}</th>
                                        <th>{{ trans('admin/main.paid_amount') }}</th>
                                        <th class="text-left">{{ trans('admin/main.item') }}</th>
                                        <th>{{ trans('admin/main.date') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th width="140">{{ trans('admin/main.actions') }}</th>
                                    </tr>
                                    @forelse($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td class="text-left">
                                                {{ !empty($sale->buyer) ? $sale->buyer->full_name : '-' }}
                                                @if(!empty($sale->buyer))
                                                    <div class="text-primary text-small">ID: {{ $sale->buyer->id }}</div>
                                                @endif
                                            </td>
                                            <td class="text-left">
                                                {{ $sale->item_seller ?? '-' }}
                                                @if(!empty($sale->seller_id))
                                                    <div class="text-primary text-small">ID: {{ $sale->seller_id }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($sale->total_amount))
                                                    {{ handlePrice($sale->total_amount) }}
                                                @else
                                                    <span class="text-muted">{{ trans('public.free') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-left">
                                                <div>{{ $sale->item_title ?? trans('update.deleted_item') }}</div>
                                                @if(!empty($sale->batch_name))
                                                    <div class="text-muted text-small">{{ trans('admin/main.batch') ?? 'Batch' }}: {{ $sale->batch_name }}</div>
                                                @endif
                                                <div class="text-primary text-small">ID: {{ $sale->item_id }}</div>
                                            </td>
                                            <td>{{ dateTimeFormat($sale->created_at, 'j F Y H:i') }}</td>
                                            <td>
                                                @if(!empty($sale->refund_at))
                                                    <span class="text-warning">{{ trans('admin/main.refund') }}</span>
                                                @elseif(!$sale->access_to_purchased_item)
                                                    <span class="text-danger">{{ trans('update.access_blocked') }}</span>
                                                @else
                                                    <span class="text-success">{{ trans('admin/main.success') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('admin_enrollment_block_access')
                                                    @if(empty($sale->refund_at) && $sale->access_to_purchased_item)
                                                        <a href="{{ getAdminPanelUrl() }}/enrollments/{{ $sale->id }}/block-access" class="btn btn-sm btn-danger" title="{{ trans('update.access_blocked') }}" onclick="return confirm('{{ trans('admin/main.are_you_sure') }}');"><i class="fa fa-ban"></i></a>
                                                    @endif
                                                @endcan
                                                @can('admin_enrollment_enable_access')
                                                    @if(empty($sale->refund_at) && !$sale->access_to_purchased_item)
                                                        <a href="{{ getAdminPanelUrl() }}/enrollments/{{ $sale->id }}/enable-access" class="btn btn-sm btn-success" title="{{ trans('update.enable_access') }}"><i class="fa fa-check"></i></a>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">{{ trans('admin/main.no_result') }}</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            {{ $sales->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
@endpush
