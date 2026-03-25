<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Recipe: meal plan / food recipe with nutritional data.
 * Used only for dietician recommendations (Recommended Meals / Diet Plan). Not part of courses.
 */
class Recipe extends Model
{
    const MEAL_TYPE_BREAKFAST = 'breakfast';
    const MEAL_TYPE_LUNCH = 'lunch';
    const MEAL_TYPE_DINNER = 'dinner';
    const MEAL_TYPE_SNACK = 'snack';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static $mealTypes = [
        self::MEAL_TYPE_BREAKFAST,
        self::MEAL_TYPE_LUNCH,
        self::MEAL_TYPE_DINNER,
        self::MEAL_TYPE_SNACK,
    ];

    protected $table = 'recipes';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    protected $casts = [
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
    ];

    public function assignments()
    {
        return $this->hasMany(StudentRecipeAssignment::class, 'recipe_id', 'id');
    }
}
