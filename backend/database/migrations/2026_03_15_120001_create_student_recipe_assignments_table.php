<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Dietician assigns recipes to students. Separate from courses – recommended meals / diet plan only.
     */
    public function up()
    {
        if (Schema::hasTable('student_recipe_assignments')) {
            return;
        }
        Schema::create('student_recipe_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('recipe_id')->index();
            $table->unsignedBigInteger('assigned_by')->index(); // dietician user_id
            $table->date('assigned_for_date')->nullable()->index(); // which day this recipe is for (e.g. plan day)
            $table->unsignedSmallInteger('day_number')->nullable(); // optional: Day 1, Day 2 in plan
            $table->string('meal_slot', 32)->nullable(); // breakfast, lunch, dinner, snack – override recipe default
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_at')->nullable();
            $table->unsignedInteger('updated_at')->nullable();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_recipe_assignments');
    }
};
