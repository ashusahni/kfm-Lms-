<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Api\Controller;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    protected $allowedTypes = [
        'blood_report' => ['pdf', 'jpg', 'jpeg', 'png'],
        'medical_report' => ['pdf', 'jpg', 'jpeg', 'png'],
        'body_photos' => ['jpg', 'jpeg', 'png'],
        'medication_prescription' => ['pdf', 'jpg', 'jpeg', 'png'],
    ];

    public function store(Request $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'));
        }

        $request->validate([
            'blood_report' => 'nullable|file|max:10240', // 10MB
            'medical_report' => 'nullable|file|max:10240',
            'body_photos.*' => 'nullable|image|max:5120', // 5MB per image
            'medication_prescription' => 'nullable|file|max:10240',
        ]);

        $record = FileUpload::firstOrNew(['user_id' => $user->id]);
        $record->user_id = $user->id;

        foreach (['blood_report', 'medical_report', 'medication_prescription'] as $key) {
            if ($request->hasFile($key)) {
                if ($record->$key) {
                    Storage::disk('public')->delete($record->$key);
                }
                $path = $request->file($key)->store('onboarding/' . $user->id, 'public');
                $record->$key = $path;
            }
        }

        if ($request->hasFile('body_photos')) {
            $paths = [];
            foreach ($request->file('body_photos') as $file) {
                $paths[] = $file->store('onboarding/' . $user->id . '/body', 'public');
            }
            $existing = $record->body_photos ? (is_string($record->body_photos) ? explode(',', $record->body_photos) : json_decode($record->body_photos, true) ?? []) : [];
            $record->body_photos = implode(',', array_merge($existing, $paths));
        }

        $record->save();

        $urls = [
            'blood_report' => $record->blood_report ? Storage::disk('public')->url($record->blood_report) : null,
            'medical_report' => $record->medical_report ? Storage::disk('public')->url($record->medical_report) : null,
            'medication_prescription' => $record->medication_prescription ? Storage::disk('public')->url($record->medication_prescription) : null,
        ];
        $bodyUrls = $record->body_photos
            ? array_map(fn ($p) => Storage::disk('public')->url(trim($p)), explode(',', $record->body_photos))
            : [];
        $urls['body_photos'] = $bodyUrls;

        return apiResponse2(1, 'stored', trans('api.public.stored'), ['files' => $urls]);
    }
}
