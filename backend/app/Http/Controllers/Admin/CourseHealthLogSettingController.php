<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseHealthLogSetting;
use App\Models\Webinar;
use Illuminate\Http\Request;

/**
 * Admin: configure health log settings per course (tracking notes from description, custom fields).
 */
class CourseHealthLogSettingController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_general_dashboard_show');

        $query = Webinar::query()
            ->whereIn('status', ['active', 'pending']);

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($t) use ($search) {
                    $t->where('title', 'like', '%' . $search . '%');
                })->orWhere('id', $search);
            });
        }

        $query->orderBy('id');
        $webinars = $query->paginate(20)->appends($request->query());
        $settingIds = CourseHealthLogSetting::whereIn('webinar_id', $webinars->pluck('id'))->pluck('webinar_id')->toArray();

        $data = [
            'pageTitle' => trans('admin/main.course_health_log_settings') ?? 'Course health log settings',
            'webinars' => $webinars,
            'settingIds' => $settingIds,
        ];

        return view('admin.course_health_log_settings.index', $data);
    }

    public function edit($webinar_id)
    {
        $this->authorize('admin_general_dashboard_show');

        $webinar = Webinar::findOrFail($webinar_id);
        $setting = CourseHealthLogSetting::firstOrNew(['webinar_id' => $webinar_id]);
        if (!$setting->exists) {
            $setting->enable_health_log = true;
            $setting->tracking_notes = $webinar->description ? strip_tags(\Str::limit($webinar->description, 2000)) : '';
            $setting->custom_fields = [];
        }

        $data = [
            'pageTitle' => trans('admin/main.course_health_log_settings') ?? 'Course health log settings',
            'webinar' => $webinar,
            'setting' => $setting,
        ];

        return view('admin.course_health_log_settings.edit', $data);
    }

    public function update(Request $request, $webinar_id)
    {
        $this->authorize('admin_general_dashboard_show');

        $webinar = Webinar::findOrFail($webinar_id);

        $request->validate([
            'enable_health_log' => 'nullable|in:0,1',
            'tracking_notes' => 'nullable|string|max:10000',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.key' => 'required_with:custom_fields|string|max:64',
            'custom_fields.*.label' => 'required_with:custom_fields|string|max:128',
            'custom_fields.*.type' => 'required_with:custom_fields|in:number,text',
        ]);

        $customFields = [];
        foreach ($request->input('custom_fields', []) as $row) {
            if (!empty($row['key']) && !empty($row['label'])) {
                $customFields[] = [
                    'key' => \Str::slug($row['key']) ?: $row['key'],
                    'label' => $row['label'],
                    'type' => $row['type'] ?? 'text',
                ];
            }
        }

        $setting = CourseHealthLogSetting::firstOrNew(['webinar_id' => $webinar_id]);
        $now = time();
        $setting->enable_health_log = $request->has('enable_health_log') ? (bool) $request->enable_health_log : true;
        $setting->tracking_notes = $request->tracking_notes ?: null;
        $setting->custom_fields = $customFields;
        $setting->updated_at = $now;
        if (!$setting->exists) {
            $setting->created_at = $now;
        }
        $setting->save();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.saved') ?? 'Settings saved.',
            'status' => 'success',
        ];

        return redirect(getAdminPanelUrl() . '/course-health-log-settings')->with(['toast' => $toastData]);
    }
}
