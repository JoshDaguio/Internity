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
            $table->foreignId('student_id')->constrained('users');
            $table->timestamp('morning_in')->nullable();
            $table->timestamp('morning_out')->nullable();
            $table->timestamp('afternoon_in')->nullable();
            $table->timestamp('afternoon_out')->nullable();
            $table->integer('total_absences')->default(0);
            $table->integer('total_tardiness')->default(0);
            $table->integer('total_make_up_hours')->default(0);
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
