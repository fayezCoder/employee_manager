<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('national_id', 50)->unique();
            $table->string('job_title', 150)->nullable();
            $table->foreignId('org_unit_id')->nullable()->constrained('organizational_units')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('needs_review')->default(false);
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index('org_unit_id');
            $table->index('user_id');
            $table->index('is_active');
            $table->index('needs_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
