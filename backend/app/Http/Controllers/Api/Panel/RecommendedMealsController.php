<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\StudentRecipeAssignment;
use Illuminate\Http\Request;

/**
 * Student view: "Recommended Meals" / "Diet Plan" – recipes assigned by dietician only.
 * Completely separate from courses. No drip logic.
 */
class RecommendedMealsController extends Controller
{
    /**
     * List my assigned recipes (Recommended Meals / Diet Plan).
     * GET /panel/recommended-meals
     * Query: from_date, to_date (optional filters by assigned_for_date).
     */
    public function index(Request $request)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'), null);
        }

        $query = StudentRecipeAssignment::where('student_id', $user->id)
            ->with(['recipe', 'assignedByUser:id,full_name']);
        if ($request->filled('from_date')) {
            $query->where('assigned_for_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->where('assigned_for_date', '<=', $request->input('to_date'));
        }
        $assignments = $query->orderBy('assigned_for_date')->orderBy('day_number')->orderBy('id')->get();

        $items = $assignments->map(function ($a) {
            $recipe = $a->recipe;
            return [
                'assignment_id' => $a->id,
                'assigned_for_date' => $a->assigned_for_date,
                'day_number' => $a->day_number,
                'meal_slot' => $a->meal_slot ?? $recipe->meal_type ?? null,
                'notes' => $a->notes,
                'assigned_by_name' => $a->assignedByUser ? $a->assignedByUser->full_name : null,
                'recipe' => $recipe ? [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'description' => $recipe->description,
                    'ingredients' => $recipe->ingredients,
                    'calories' => $recipe->calories,
                    'protein' => $recipe->protein,
                    'carbs' => $recipe->carbs,
                    'fats' => $recipe->fats,
                    'meal_type' => $recipe->meal_type,
                    'preparation_video' => $recipe->preparation_video,
                    'instructions' => $recipe->instructions,
                    'image' => $recipe->image,
                ] : null,
            ];
        });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'title' => 'Recommended Meals',
            'assignments' => $items,
        ]);
    }

    /**
     * Get one assigned recipe detail (by assignment id).
     * GET /panel/recommended-meals/{assignmentId}
     */
    public function show($assignmentId)
    {
        $user = apiAuth();
        if (!$user) {
            return apiResponse2(0, 'unauthorized', trans('auth.unauthorized'), null);
        }

        $assignment = StudentRecipeAssignment::where('student_id', $user->id)
            ->where('id', $assignmentId)
            ->with(['recipe', 'assignedByUser:id,full_name'])
            ->first();
        if (!$assignment) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }

        $recipe = $assignment->recipe;
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'assignment_id' => $assignment->id,
            'assigned_for_date' => $assignment->assigned_for_date,
            'day_number' => $assignment->day_number,
            'meal_slot' => $assignment->meal_slot ?? ($recipe ? $recipe->meal_type : null),
            'notes' => $assignment->notes,
            'assigned_by_name' => $assignment->assignedByUser ? $assignment->assignedByUser->full_name : null,
            'recipe' => $recipe ? [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description,
                'ingredients' => $recipe->ingredients,
                'calories' => $recipe->calories,
                'protein' => $recipe->protein,
                'carbs' => $recipe->carbs,
                'fats' => $recipe->fats,
                'meal_type' => $recipe->meal_type,
                'preparation_video' => $recipe->preparation_video,
                'instructions' => $recipe->instructions,
                'image' => $recipe->image,
            ] : null,
        ]);
    }
}
