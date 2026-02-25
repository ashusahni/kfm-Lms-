<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Per-course health log config: description-based tracking notes and optional custom fields.
     */
    public function up()
    {
        Schema::create('course_health_log_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('webinar_id')->unique()->index();
            $table->boolean('enable_health_log')->default(true);
            $table->text('tracking_notes')->nullable()->comment('Instructions/description for what to log in this course');
            $table->json('custom_fields')->nullable()->comment('[{ "key": "weight", "label": "Weight (kg)", "type": "number" }]');
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('course_health_log_settings');
    }
};
