<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthOnboardingTables extends Migration
{
    public function up()
    {
        // Drop if exists so migration can be re-run after a partial failure (e.g. FK error)
        Schema::dropIfExists('file_uploads');
        Schema::dropIfExists('user_body_goals');
        Schema::dropIfExists('lifestyle_assessments');
        Schema::dropIfExists('diet_patterns');
        Schema::dropIfExists('medical_data');
        Schema::dropIfExists('user_health_conditions');
        Schema::dropIfExists('health_profiles');
        Schema::dropIfExists('body_goals');
        Schema::dropIfExists('health_conditions');

        Schema::create('health_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('body_goals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('health_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->integer('age')->nullable();
            $table->string('gender', 32)->nullable();
            $table->decimal('height', 5, 2)->nullable()->comment('in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('in kg');
            $table->string('city', 128)->nullable();
            $table->string('occupation', 128)->nullable();
            $table->string('lifestyle_type', 32)->nullable(); // sedentary, moderate, active
            $table->string('language', 64)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_health_conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('condition_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('condition_id')->references('id')->on('health_conditions')->onDelete('cascade');
            $table->unique(['user_id', 'condition_id']);
        });

        Schema::create('medical_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->text('current_medications')->nullable();
            $table->text('past_surgeries')->nullable();
            $table->text('food_allergies')->nullable();
            $table->text('menstrual_history')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('diet_patterns', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->string('diet_type', 32)->nullable(); // veg, nonveg, eggetarian
            $table->string('meal_pattern', 64)->nullable(); // north_indian, south_indian
            $table->string('breakfast', 512)->nullable();
            $table->string('lunch', 512)->nullable();
            $table->string('dinner', 512)->nullable();
            $table->string('food_cravings', 512)->nullable();
            $table->string('outside_food_frequency', 64)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('lifestyle_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->decimal('sleep_hours', 3, 1)->nullable();
            $table->string('stress_level', 32)->nullable();
            $table->string('water_intake', 64)->nullable();
            $table->string('physical_activity_level', 64)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_body_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('goal_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('goal_id')->references('id')->on('body_goals')->onDelete('cascade');
            $table->unique(['user_id', 'goal_id']);
        });

        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('blood_report', 256)->nullable();
            $table->string('medical_report', 256)->nullable();
            $table->string('body_photos', 512)->nullable(); // comma-separated paths or JSON
            $table->string('medication_prescription', 256)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_uploads');
        Schema::dropIfExists('user_body_goals');
        Schema::dropIfExists('lifestyle_assessments');
        Schema::dropIfExists('diet_patterns');
        Schema::dropIfExists('medical_data');
        Schema::dropIfExists('user_health_conditions');
        Schema::dropIfExists('health_profiles');
        Schema::dropIfExists('body_goals');
        Schema::dropIfExists('health_conditions');
    }
}
