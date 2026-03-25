<?php

namespace App\Models\Api\Traits;

trait UploaderTrait
{
    /**
     * Store file on local (system) storage. Used for course-related uploads
     * (e.g. forum attachments, assignment messages) so they use device storage.
     */
    public function storage($file)
    {
        if (!$file) {
            return null;
        }
        $fileName = $file->getClientOriginalName();
        $path = apiAuth()->id;
        $storage_path = $file->storeAs($path, $fileName, 'upload');
        return 'store/' . $storage_path;
    }
}
