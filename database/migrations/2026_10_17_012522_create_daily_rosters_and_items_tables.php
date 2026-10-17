<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_unit_id')->constrained('organizational_units')->restrictOnDelete();
            $table->date('date');
            // Explicit workflow states: draft, center_locked, completed
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('version')->default(1);

            // Snapshot data at completion / approval time
            $table->string('unit_name_snapshot', 255)->nullable();
            $table->string('unit_code_snapshot', 50)->nullable();
            $table->string('unit_type_snapshot', 30)->nullable();
            $table->string('unit_path_snapshot', 500)->nullable();

            // Tracking users & timestamps
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('branch_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('branch_approved_at')->nullable();

            $table->foreignId('fully_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fully_approved_at')->nullable();

            $table->boolean('was_corrected')->default(false);
            $table->timestamps();

            // Constraint: Exactly one roster per organizational unit per date
            $table->unique(['org_unit_id', 'date'], 'uq_roster_unit_date');
            $table->index('date');
            $table->index('status');
        });

        Schema::create('daily_roster_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_roster_id')->constrained('daily_rosters')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->date('date'); // Denormalized from daily_rosters to enforce DB-level unique constraint per date

            // Status: null until selected by the user. 14 possible Arabic states:
            // حاضر, غايب, مستأذن, موعد, مكلف, متأخر, موقوف, انتداب, دورة, إجازة اعتيادية, إجازة عرضية, إجازة ميدانية, إجازة مرضية, أخرى
            $table->string('status', 40)->nullable();
            $table->string('notes', 500)->nullable();

            // Flat snapshot fields of employee identity at roster time
            $table->string('employee_name_snapshot', 255)->nullable();
            $table->string('employee_national_id_snapshot', 50)->nullable();
            $table->string('employee_job_title_snapshot', 150)->nullable();
            $table->timestamps();

            // Constraint: Employee can only appear once in a given roster
            $table->unique(['daily_roster_id', 'employee_id'], 'uq_item_roster_employee');
            // Constraint: Employee CANNOT appear in two rosters across any units on the same date!
            $table->unique(['employee_id', 'date'], 'uq_item_employee_date');
            $table->index('status');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_roster_items');
        Schema::dropIfExists('daily_rosters');
    }
};
