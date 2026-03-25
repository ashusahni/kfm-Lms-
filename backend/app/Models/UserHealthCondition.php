<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserHealthCondition extends Model
{
    protected $table = 'user_health_conditions';

    protected $fillable = ['user_id', 'condition_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(HealthCondition::class, 'condition_id');
    }
}
