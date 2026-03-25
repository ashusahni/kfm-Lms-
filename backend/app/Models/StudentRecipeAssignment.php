<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Dietician assigns a recipe to a student for a given day/slot.
 * Separate from courses – "Recommended Meals" / "Diet Plan" only.
 */
class StudentRecipeAssignment extends Model
{
    protected $table = 'student_recipe_assignments';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(\App\User::class, 'student_id', 'id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(\App\User::class, 'assigned_by', 'id');
    }
}
