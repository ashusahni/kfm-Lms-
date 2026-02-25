<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Course-specific custom field values (e.g. weight, sleep_hours).
     */
    public function up()
    {
        Schema::table('student_daily_health_logs', function (Blueprint $table) {
            $table->json('custom_data')->nullable()->after('adherence_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('student_daily_health_logs', function (Blueprint $table) {
            $table->dropColumn('custom_data');
        });
    }
};
