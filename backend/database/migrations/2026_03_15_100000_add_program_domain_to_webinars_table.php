<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgramDomainToWebinarsTable extends Migration
{
    /**
     * Program domain for health/fitness LMS: general, health, fitness, nutrition, wellness.
     * Used to show the right course structure (e.g. intake form, health logs) and filtering.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('webinars')) {
            return;
        }
        Schema::table('webinars', function (Blueprint $table) {
            if (!Schema::hasColumn('webinars', 'program_domain')) {
                $table->string('program_domain', 32)->nullable()->default('general')
                    ->after('challenge_type')
                    ->comment('general, health, fitness, nutrition, wellness');
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
        if (!Schema::hasTable('webinars') || !Schema::hasColumn('webinars', 'program_domain')) {
            return;
        }
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn('program_domain');
        });
    }
}
