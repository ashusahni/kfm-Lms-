<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChallengeTypeToWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     * Challenge courses: 7-day or 30-day, released in batches by start date (e.g. Batch 1 Mar, Batch 5 Mar).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('webinars')) {
            return;
        }

        Schema::table('webinars', function (Blueprint $table) {
            if (!Schema::hasColumn('webinars', 'challenge_type')) {
                $table->string('challenge_type', 20)->nullable()->after('type')->comment('null, 7_days, 30_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('webinars') || !Schema::hasColumn('webinars', 'challenge_type')) {
            return;
        }

        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn('challenge_type');
        });
    }
}
