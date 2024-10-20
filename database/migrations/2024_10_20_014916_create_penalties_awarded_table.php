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
        Schema::create('penalties_awarded', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users'); // Refers to the student user
            $table->foreignId('penalty_id')->constrained('penalties'); // Refers to the violation penalty
            $table->foreignId('dtr_id')->nullable()->constrained('daily_time_records'); // If related to a specific day
            $table->date('awarded_date'); // Date when the penalty was awarded
            $table->integer('penalty_hours'); // Hours added for this penalty
            $table->text('remarks')->nullable(); // Any additional remarks by the admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties_awarded');
    }
};
