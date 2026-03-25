<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FileUpload extends Model
{
    protected $table = 'file_uploads';

    protected $fillable = [
        'user_id', 'blood_report', 'medical_report', 'body_photos', 'medication_prescription',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
