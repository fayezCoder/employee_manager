<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->enum('status', ['open', 'complete', 'incomplete'])->default('open');
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index('date');
            $table->index('status');
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
