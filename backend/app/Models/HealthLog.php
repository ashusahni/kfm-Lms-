<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthLog extends Model
{
    protected $table = 'health_logs';

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $fillable = ['check_name', 'status', 'message', 'meta', 'created_at'];

    protected $casts = [
        'meta' => 'array',
    ];

    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_FAILED = 'failed';
}
