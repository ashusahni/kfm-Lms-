<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCondition extends Model
{
    protected $table = 'health_conditions';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'user_health_conditions', 'condition_id', 'user_id')
            ->withTimestamps();
    }
}
