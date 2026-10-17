<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_unit_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // org_unit_id is nullable if super_admin applies globally without specific unit binding
            $table->foreignId('org_unit_id')->nullable()->constrained('organizational_units')->cascadeOnDelete();
            // Roles: super_admin, main_command_admin, branch_command_admin, center_admin, division_admin, department_admin
            $table->string('role', 40);
            $table->boolean('include_sub_branches')->default(false);
            $table->boolean('can_edit_approved')->default(false);
            $table->boolean('can_manage_employees')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'org_unit_id', 'role'], 'user_unit_role_unique');
            $table->index(['user_id', 'role']);
            $table->index('org_unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_unit_assignments');
    }
};
