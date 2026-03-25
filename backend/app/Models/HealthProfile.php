<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class HealthProfile extends Model
{
    protected $table = 'health_profiles';

    protected $fillable = [
        'user_id', 'age', 'gender', 'height', 'weight', 'city',
        'occupation', 'lifestyle_type', 'language',
    ];

    protected $casts = [
        'age' => 'integer',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
