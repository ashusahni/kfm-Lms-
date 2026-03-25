<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseIntakesTable extends Migration
{
    public function up()
    {
        Schema::create('course_intakes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('webinar_id');
            $table->text('weight_history')->nullable();
            $table->text('past_dieting_attempts')->nullable();
            $table->text('digestive_issues')->nullable();
            $table->string('sleep_quality', 128)->nullable();
            $table->string('stress_level', 128)->nullable();
            $table->text('food_preference')->nullable();
            $table->text('meal_timings')->nullable();
            $table->text('daily_schedule')->nullable();
            $table->string('blood_reports', 512)->nullable()->comment('comma-separated file paths');
            $table->string('body_measurements', 512)->nullable()->comment('file path or JSON');
            $table->string('body_photos', 512)->nullable()->comment('comma-separated file paths');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->unique(['user_id', 'webinar_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_intakes');
    }
}
