<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCourseLearningNotesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('course_learning_notes');
    }

    public function down()
    {
        Schema::create('course_learning_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('webinar_id');
            $table->string('item_type', 50);
            $table->unsignedInteger('item_id');
            $table->text('note')->nullable();
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->unique(['user_id', 'webinar_id', 'item_type', 'item_id']);
        });
    }
}
