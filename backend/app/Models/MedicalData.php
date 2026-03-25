<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MedicalData extends Model
{
    protected $table = 'medical_data';

    protected $fillable = [
        'user_id', 'current_medications', 'past_surgeries',
        'food_allergies', 'menstrual_history',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
