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
        Schema::create('end_of_day_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->text('key_successes');
            $table->text('main_challenges');
            $table->text('plans_for_tomorrow');
            $table->timestamp('date_submitted');
            $table->timestamps();
        });

        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('end_of_day_reports')->onDelete('cascade');
            $table->text('task_description');
            $table->integer('time_spent');
            $table->enum('time_unit', ['minutes', 'hours']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
        Schema::dropIfExists('end_of_day_reports');
    }
};
