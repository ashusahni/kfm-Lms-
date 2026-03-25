<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class SeedHealthConditionsAndBodyGoals extends Migration
{
    public function up()
    {
        $conditions = [
            'Thyroid', 'Diabetes', 'PCOS', 'Hypertension', 'Fertility Issues',
            'Hair fall', 'Skin Issues', 'PCOD', 'Constipation', 'GERD', 'Acidity',
            'Kidney Health', 'Liver Health', 'Fat Loss', 'Weight Gain', 'Other',
        ];
        foreach ($conditions as $name) {
            DB::table('health_conditions')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }

        $goals = [
            'Fat loss', 'Inch loss', 'PCOS management', 'Thyroid support',
            'Gut health', 'Hair fall', 'Skin health',
        ];
        foreach ($goals as $name) {
            DB::table('body_goals')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        DB::table('health_conditions')->truncate();
        DB::table('body_goals')->truncate();
    }
}
