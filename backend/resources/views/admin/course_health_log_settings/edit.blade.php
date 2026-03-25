@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/course-health-log-settings">{{ trans('admin/main.course_health_log_settings') ?? 'Course health log settings' }}</a></div>
                <div class="breadcrumb-item">{{ $webinar->title }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $webinar->title }} (ID: {{ $webinar->id }})</h4>
                </div>
                <form method="post" action="{{ getAdminPanelUrl() }}/course-health-log-settings/{{ $webinar->id }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="enable_health_log" name="enable_health_log" value="1" {{ ($setting->enable_health_log ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="enable_health_log">{{ trans('admin/main.enable_health_log_for_this_course') ?? 'Enable health log for this course' }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('admin/main.tracking_notes') ?? 'Tracking notes / instructions' }}</label>
                            <p class="text-muted font-12">{{ trans('admin/main.tracking_notes_help') ?? 'Based on course description: what students should log (e.g. water, meals, activity). Shown to students when they log for this course.' }}</p>
                            <textarea name="tracking_notes" class="form-control" rows="6" placeholder="{{ trans('admin/main.paste_course_description_or_instructions') ?? 'Paste course description or custom instructions...' }}">{{ old('tracking_notes', $setting->tracking_notes ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('admin/main.custom_fields') ?? 'Custom fields' }}</label>
                            <p class="text-muted font-12">{{ trans('admin/main.custom_fields_help') ?? 'Optional extra fields per log (e.g. Weight kg, Sleep hours). Key must be unique (e.g. weight_kg).' }}</p>
                            <div id="custom-fields-container">
                                @foreach(old('custom_fields', $setting->custom_fields ?? []) as $i => $field)
                                    <div class="row mb-2 custom-field-row">
                                        <div class="col-md-3">
                                            <input type="text" name="custom_fields[{{ $i }}][key]" class="form-control form-control-sm" placeholder="key (e.g. weight_kg)" value="{{ $field['key'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="custom_fields[{{ $i }}][label]" class="form-control form-control-sm" placeholder="Label" value="{{ $field['label'] ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <select name="custom_fields[{{ $i }}][type]" class="form-control form-control-sm">
                                                <option value="text" {{ (($field['type'] ?? '') == 'text') ? 'selected' : '' }}>Text</option>
                                                <option value="number" {{ (($field['type'] ?? '') == 'number') ? 'selected' : '' }}>Number</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-custom-field"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-custom-field">
                                <i class="fas fa-plus"></i> {{ trans('admin/main.add_field') ?? 'Add field' }}
                            </button>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('public.save') ?? 'Save' }}</button>
                        <a href="{{ getAdminPanelUrl() }}/course-health-log-settings" class="btn btn-secondary">{{ trans('public.cancel') ?? 'Cancel' }}</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
<script>
(function() {
    var index = {{ count(old('custom_fields', $setting->custom_fields ?? [])) }};
    document.getElementById('add-custom-field').addEventListener('click', function() {
        var html = '<div class="row mb-2 custom-field-row">' +
            '<div class="col-md-3"><input type="text" name="custom_fields[' + index + '][key]" class="form-control form-control-sm" placeholder="key"></div>' +
            '<div class="col-md-4"><input type="text" name="custom_fields[' + index + '][label]" class="form-control form-control-sm" placeholder="Label"></div>' +
            '<div class="col-md-3"><select name="custom_fields[' + index + '][type]" class="form-control form-control-sm"><option value="text">Text</option><option value="number">Number</option></select></div>' +
            '<div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger remove-custom-field"><i class="fas fa-times"></i></button></div></div>';
        document.getElementById('custom-fields-container').insertAdjacentHTML('beforeend', html);
        index++;
    });
    document.getElementById('custom-fields-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-custom-field')) {
            e.target.closest('.custom-field-row').remove();
        }
    });
})();
</script>
@endpush
