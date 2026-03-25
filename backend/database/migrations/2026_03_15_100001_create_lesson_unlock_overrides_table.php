<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonUnlockOverridesTable extends Migration
{
    /**
     * Run the migrations.
     * Manual unlock: for specific user, specific group, or all users.
     * When user_id and group_id are both null = unlock for all users.
     */
    public function up()
    {
        Schema::create('lesson_unlock_overrides', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('webinar_id');
            $table->string('content_type', 32);
            $table->unsignedInteger('content_id');

            $table->unsignedInteger('user_id')->nullable(); // null = not user-specific
            $table->unsignedInteger('group_id')->nullable(); // e.g. discount_group_id; null = not group-specific
            // When both null = unlock for everyone (manual unlock all)

            $table->unsignedInteger('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable(); // admin user who applied override

            $table->unique(
                ['webinar_id', 'content_type', 'content_id', 'user_id', 'group_id'],
                'lesson_unlock_overrides_unique'
            );
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_unlock_overrides');
    }
}
