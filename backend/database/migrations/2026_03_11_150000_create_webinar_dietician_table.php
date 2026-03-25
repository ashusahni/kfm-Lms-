<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarDieticianTable extends Migration
{
    /**
     * Run the migrations.
     * Owner assigns courses to dietitians; dietitians see only assigned courses in their panel.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_dietician', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->unsignedInteger('webinar_id');
            $table->unsignedInteger('user_id');

            $table->unique(['webinar_id', 'user_id']);
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_dietician');
    }
}
