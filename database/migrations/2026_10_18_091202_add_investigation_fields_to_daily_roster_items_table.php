<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_roster_items', function (Blueprint $table) {
            $table->string('investigation_number')->nullable()->after('notes');
            $table->string('investigation_status')->nullable()->after('investigation_number'); // 'completed', 'pending'
            $table->text('investigation_notes')->nullable()->after('investigation_status');
            $table->timestamp('investigated_at')->nullable()->after('investigation_notes');
            $table->foreignId('investigated_by')->nullable()->after('investigated_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_roster_items', function (Blueprint $table) {
            $table->dropForeign(['investigated_by']);
            $table->dropColumn([
                'investigation_number',
                'investigation_status',
                'investigation_notes',
                'investigated_at',
                'investigated_by',
            ]);
        });
    }
};
