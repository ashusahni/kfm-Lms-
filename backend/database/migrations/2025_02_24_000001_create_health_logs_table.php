<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_logs', function (Blueprint $table) {
            $table->id();
            $table->string('check_name', 100)->index();
            $table->string('status', 50)->default('ok'); // ok, warning, failed
            $table->string('message', 500)->nullable();
            $table->text('meta')->nullable(); // JSON details
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_logs');
    }
};
