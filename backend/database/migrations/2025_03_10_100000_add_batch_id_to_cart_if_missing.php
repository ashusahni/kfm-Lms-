<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchIdToCartIfMissing extends Migration
{
    /**
     * Run the migrations.
     * Adds batch_id to cart table if missing (e.g. when add-to-cart uses batch_id in WHERE).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cart')) {
            return;
        }

        if (Schema::hasColumn('cart', 'batch_id')) {
            return;
        }

        // course_batches must exist for the foreign key
        if (!Schema::hasTable('course_batches')) {
            return;
        }

        Schema::table('cart', function (Blueprint $table) {
            $table->integer('batch_id')->unsigned()->nullable()->after('ticket_id');
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
        if (!Schema::hasTable('cart') || !Schema::hasColumn('cart', 'batch_id')) {
            return;
        }

        Schema::table('cart', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
}
