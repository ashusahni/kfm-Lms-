<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequentialModuleUnlockToWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     * When true, students can only access one chapter (week/module) at a time;
     * the next chapter unlocks after completing all content in the current one.
     */
    public function up()
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->boolean('sequential_module_unlock')->default(false)->after('access_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn('sequential_module_unlock');
        });
    }
}
