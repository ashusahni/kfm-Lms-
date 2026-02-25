<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fit Karnataka: Daily Health Challenge – one row per student per day (per program optional).
     */
    public function up()
    {
        Schema::create('student_daily_health_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('webinar_id')->nullable()->index(); // program/course
            $table->date('log_date')->index();
            $table->unsignedInteger('water_ml')->nullable();
            $table->json('meals')->nullable(); // { breakfast: "", lunch: "", dinner: "", snacks: "" }
            $table->unsignedInteger('calories')->nullable();
            $table->unsignedInteger('protein')->nullable();
            $table->unsignedInteger('carbs')->nullable();
            $table->unsignedInteger('fat')->nullable();
            $table->text('medicines')->nullable();
            $table->unsignedInteger('activity_minutes')->nullable();
            $table->string('activity_notes', 500)->nullable();
            $table->unsignedTinyInteger('adherence_score')->nullable(); // 0-100
            $table->unsignedInteger('locked_at')->nullable(); // after this timestamp student cannot edit
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at')->nullable();
            $table->unique(['user_id', 'webinar_id', 'log_date'], 'student_daily_health_logs_user_webinar_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('student_daily_health_logs');
    }
};
