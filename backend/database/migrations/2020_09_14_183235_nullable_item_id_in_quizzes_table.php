<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableItemIdInQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            \DB::statement('ALTER TABLE `quizzes` MODIFY `item_id` INTEGER UNSIGNED NULL;');
        } else {
            // PostgreSQL and others: no backticks, use ALTER COLUMN
            \DB::statement('ALTER TABLE quizzes ALTER COLUMN item_id DROP NOT NULL;');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->integer('item_id')->unsigned()->change();
            });
        } else {
            \DB::statement('ALTER TABLE quizzes ALTER COLUMN item_id SET NOT NULL;');
        }
    }
}
