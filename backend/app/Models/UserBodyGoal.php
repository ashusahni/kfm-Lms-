<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserBodyGoal extends Model
{
    protected $table = 'user_body_goals';

    protected $fillable = ['user_id', 'goal_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goal()
    {
        return $this->belongsTo(BodyGoal::class, 'goal_id');
    }
}
