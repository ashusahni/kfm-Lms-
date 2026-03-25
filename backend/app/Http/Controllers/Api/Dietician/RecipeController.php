<?php

namespace App\Http\Controllers\Api\Dietician;

use App\Http\Controllers\Api\Controller;
use App\Models\Recipe;
use App\Models\StudentRecipeAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Dietician: manage recipes and assign them to students.
 * Recipes are NOT courses – they are recommended meals / diet plan only.
 */
class RecipeController extends Controller
{
    /**
     * List recipes (for dropdown / browse). Only active by default.
     * GET /dietician/recipes
     */
    public function index(Request $request)
    {
        $query = Recipe::query();
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', Recipe::STATUS_ACTIVE);
        }
        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->input('meal_type'));
        }
        $recipes = $query->orderBy('name')->get();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['recipes' => $recipes]);
    }

    /**
     * Get one recipe.
     * GET /dietician/recipes/{id}
     */
    public function show($id)
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['recipe' => $recipe]);
    }

    /**
     * Create a recipe (dietician or admin).
     * POST /dietician/recipes
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'meal_type' => ['nullable', Rule::in(Recipe::$mealTypes)],
            'preparation_video' => 'nullable|string|max:500',
            'instructions' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'status' => ['nullable', Rule::in([Recipe::STATUS_ACTIVE, Recipe::STATUS_INACTIVE])],
        ]);
        $valid['created_at'] = time();
        $valid['updated_at'] = time();
        $recipe = Recipe::create($valid);
        return apiResponse2(1, 'saved', trans('api.public.saved'), ['recipe' => $recipe]);
    }

    /**
     * Update a recipe.
     * PATCH /dietician/recipes/{id}
     */
    public function update(Request $request, $id)
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $valid = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'meal_type' => ['nullable', Rule::in(Recipe::$mealTypes)],
            'preparation_video' => 'nullable|string|max:500',
            'instructions' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'status' => ['nullable', Rule::in([Recipe::STATUS_ACTIVE, Recipe::STATUS_INACTIVE])],
        ]);
        $valid['updated_at'] = time();
        $recipe->fill($valid);
        $recipe->save();
        return apiResponse2(1, 'saved', trans('api.public.saved'), ['recipe' => $recipe]);
    }

    /**
     * Delete a recipe.
     * DELETE /dietician/recipes/{id}
     */
    public function destroy($id)
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $recipe->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'), null);
    }

    /**
     * List recipe assignments for a student (dietician view).
     * GET /dietician/students/{studentId}/recipe-assignments
     */
    public function assignmentsForStudent(Request $request, $studentId)
    {
        $query = StudentRecipeAssignment::where('student_id', $studentId)
            ->with(['recipe', 'assignedByUser:id,full_name,email']);
        if ($request->filled('from_date')) {
            $query->where('assigned_for_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->where('assigned_for_date', '<=', $request->input('to_date'));
        }
        $assignments = $query->orderBy('assigned_for_date')->orderBy('day_number')->orderBy('id')->get();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), ['assignments' => $assignments]);
    }

    /**
     * Assign a recipe to a student.
     * POST /dietician/students/{studentId}/recipe-assignments
     */
    public function assignToStudent(Request $request, $studentId)
    {
        $user = apiAuth();
        $valid = $request->validate([
            'recipe_id' => 'required|integer|exists:recipes,id',
            'assigned_for_date' => 'nullable|date',
            'day_number' => 'nullable|integer|min:1',
            'meal_slot' => ['nullable', Rule::in(Recipe::$mealTypes)],
            'notes' => 'nullable|string|max:1000',
        ]);
        $assignment = StudentRecipeAssignment::create([
            'student_id' => $studentId,
            'recipe_id' => $valid['recipe_id'],
            'assigned_by' => $user->id,
            'assigned_for_date' => $valid['assigned_for_date'] ?? null,
            'day_number' => $valid['day_number'] ?? null,
            'meal_slot' => $valid['meal_slot'] ?? null,
            'notes' => $valid['notes'] ?? null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $assignment->load(['recipe', 'assignedByUser:id,full_name']);
        return apiResponse2(1, 'saved', trans('api.public.saved'), ['assignment' => $assignment]);
    }

    /**
     * Assign a recipe to multiple students at once.
     * POST /dietician/recipes/{recipeId}/assign-bulk
     * Body: student_ids (array of user ids), assigned_for_date?, day_number?, meal_slot?, notes?
     */
    public function assignBulk(Request $request, $recipeId)
    {
        $recipe = Recipe::find($recipeId);
        if (!$recipe) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $user = apiAuth();
        $valid = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:users,id',
            'assigned_for_date' => 'nullable|date',
            'day_number' => 'nullable|integer|min:1',
            'meal_slot' => ['nullable', Rule::in(Recipe::$mealTypes)],
            'notes' => 'nullable|string|max:1000',
        ]);
        $studentIds = array_unique($valid['student_ids']);
        $created = [];
        foreach ($studentIds as $studentId) {
            $assignment = StudentRecipeAssignment::create([
                'student_id' => $studentId,
                'recipe_id' => (int) $recipeId,
                'assigned_by' => $user->id,
                'assigned_for_date' => $valid['assigned_for_date'] ?? null,
                'day_number' => $valid['day_number'] ?? null,
                'meal_slot' => $valid['meal_slot'] ?? null,
                'notes' => $valid['notes'] ?? null,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
            $assignment->load(['recipe', 'assignedByUser:id,full_name']);
            $created[] = $assignment;
        }
        return apiResponse2(1, 'saved', trans('api.public.saved'), [
            'assignments' => $created,
            'count' => count($created),
        ]);
    }

    /**
     * Remove a recipe assignment.
     * DELETE /dietician/recipe-assignments/{id}
     */
    public function removeAssignment($id)
    {
        $assignment = StudentRecipeAssignment::find($id);
        if (!$assignment) {
            return apiResponse2(0, 'not_found', trans('api.public.not_found'), null);
        }
        $assignment->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'), null);
    }
}
