<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyGoal extends Model
{
    protected $table = 'body_goals';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'user_body_goals', 'goal_id', 'user_id')
            ->withTimestamps();
    }
}
