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
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users'); // Refers to the student user
            $table->date('log_date');  // Date for the log
            $table->json('log_times')->nullable();  // Store morning in/out, afternoon in/out as JSON
            $table->integer('total_hours_worked')->default(0);
            $table->integer('remaining_hours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_time_records');
    }
};
