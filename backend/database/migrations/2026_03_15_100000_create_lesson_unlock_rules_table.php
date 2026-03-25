<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonUnlockRulesTable extends Migration
{
    /**
     * Run the migrations.
     * Stores admin-defined unlock rules per content item (lesson).
     * Content is identified by webinar_id + content_type + content_id.
     */
    public function up()
    {
        Schema::create('lesson_unlock_rules', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('webinar_id');
            $table->string('content_type', 32); // file, session, text_lesson, quiz, assignment
            $table->unsignedInteger('content_id');

            // Unlock type: none (default open), day, date, manual, sequential, delay
            $table->string('unlock_type', 32)->default('none');
            // For day-based: day number relative to enrollment (1 = first day)
            $table->unsignedInteger('unlock_day_number')->nullable();
            // For date-based: specific calendar date/time
            $table->unsignedInteger('unlock_date')->nullable();
            // For sequential: prerequisite content
            $table->string('prerequisite_content_type', 32)->nullable();
            $table->unsignedInteger('prerequisite_content_id')->nullable();
            // For delay: hours after prerequisite completion
            $table->unsignedInteger('delay_after_completion_hours')->nullable();

            // Admin overrides
            $table->boolean('is_locked')->default(false); // force lock regardless of rule
            $table->boolean('is_visible')->default(true); // hide from student list when false
            $table->unsignedInteger('scheduled_publish_at')->nullable(); // show/unlock after this timestamp

            $table->unsignedInteger('created_at')->nullable();
            $table->unsignedInteger('updated_at')->nullable();

            $table->unique(['webinar_id', 'content_type', 'content_id'], 'lesson_unlock_rules_content_unique');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_unlock_rules');
    }
}
