<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Align admin panel naming: teacher role displays as "Dietician", logic unchanged.
 */
class UpdateRoleCaptionTeacherToDietician extends Migration
{
    public function up()
    {
        DB::table('roles')->where('name', 'teacher')->update(['caption' => 'Dietician']);
    }

    public function down()
    {
        DB::table('roles')->where('name', 'teacher')->update(['caption' => 'Teacher role']);
    }
}
