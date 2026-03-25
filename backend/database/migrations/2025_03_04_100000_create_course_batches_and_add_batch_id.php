<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseBatchesAndAddBatchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_batches', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('webinar_id')->unsigned();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->integer('start_date')->unsigned()->nullable();
            $table->integer('end_date')->unsigned()->nullable();
            $table->integer('capacity')->unsigned()->nullable();
            $table->string('status', 20)->default('draft');
            $table->integer('sort_order')->default(0);
            $table->integer('created_at')->unsigned()->nullable();
            $table->integer('updated_at')->unsigned()->nullable();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->index(['webinar_id', 'status']);
            $table->index('start_date');
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->integer('batch_id')->unsigned()->nullable()->after('ticket_id');
            $table->foreign('batch_id')->references('id')->on('course_batches')->onDelete('set null');
            $table->index('batch_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('batch_id')->unsigned()->nullable()->after('webinar_id');
            $table->foreign('batch_id')->references('id')->on('course_batches')->onDelete('set null');
            $table->index('batch_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('batch_id')->unsigned()->nullable()->after('webinar_id');
            $table->foreign('batch_id')->references('id')->on('course_batches')->onDelete('set null');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        Schema::table('cart', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        Schema::dropIfExists('course_batches');
    }
}
