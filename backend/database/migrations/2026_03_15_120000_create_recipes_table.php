<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Recipes: meal plans / food recipes with nutritional data. Used for dietician recommendations only (not courses).
     */
    public function up()
    {
        if (Schema::hasTable('recipes')) {
            return;
        }
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable(); // plain text or JSON
            $table->decimal('calories', 10, 2)->nullable();
            $table->decimal('protein', 10, 2)->nullable();
            $table->decimal('carbs', 10, 2)->nullable();
            $table->decimal('fats', 10, 2)->nullable();
            $table->string('meal_type', 32)->nullable(); // breakfast, lunch, dinner, snack
            $table->string('preparation_video', 500)->nullable(); // URL or path
            $table->text('instructions')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('status', 20)->default('active'); // active, inactive
            $table->unsignedInteger('created_at')->nullable();
            $table->unsignedInteger('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipes');
    }
};
