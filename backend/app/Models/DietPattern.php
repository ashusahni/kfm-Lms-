<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DietPattern extends Model
{
    protected $table = 'diet_patterns';

    protected $fillable = [
        'user_id', 'diet_type', 'meal_pattern', 'breakfast', 'lunch', 'dinner',
        'food_cravings', 'outside_food_frequency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
