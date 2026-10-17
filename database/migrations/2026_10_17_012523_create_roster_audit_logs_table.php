<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audit logs for roster corrections after lock/approval
        Schema::create('roster_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_roster_id')->constrained('daily_rosters')->cascadeOnDelete();
            $table->foreignId('daily_roster_item_id')->nullable()->constrained('daily_roster_items')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 50); // corrected, updated
            $table->string('field_changed', 50)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('reason')->nullable(); // Mandatory explanation for corrections
            $table->unsignedInteger('roster_version')->default(1);
            $table->timestamp('created_at')->useCurrent();

            $table->index('daily_roster_id');
            $table->index('action');
            $table->index('user_id');
        });

        // 2. Workflow approval events (timeline of stage transitions)
        Schema::create('roster_workflow_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_roster_id')->constrained('daily_rosters')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_status', 30);
            $table->string('to_status', 30);
            $table->string('stage_label', 100);
            $table->text('notes')->nullable();
            $table->unsignedInteger('roster_version')->default(1);
            $table->timestamp('created_at')->useCurrent();

            $table->index('daily_roster_id');
            $table->index('user_id');
            $table->index('to_status');
        });

        // 3. Structure & permissions audit logs
        Schema::create('structure_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Actor
            $table->string('target_type', 50); // unit, user_assignment, employee
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('action', 60); // created, updated, deactivated, role_assigned, etc.
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('target_type');
            $table->index(['target_type', 'target_id']);
            $table->index('user_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('structure_audit_logs');
        Schema::dropIfExists('roster_workflow_events');
        Schema::dropIfExists('roster_audit_logs');
    }
};
