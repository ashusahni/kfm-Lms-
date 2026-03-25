<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add initial_conversation_at to sales for video course access unlock.
 * After purchase, video content is locked for 48 hours and until the dietician
 * has had an initial conversation with the student. When the dietician marks
 * the conversation done, we set initial_conversation_at; then the student can access content
 * (even before 48h if conversation was done earlier).
 */
class AddInitialConversationAtToSales extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('initial_conversation_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('initial_conversation_at');
        });
    }
}
