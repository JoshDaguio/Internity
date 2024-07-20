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
            $table->text('task_completed');
            $table->text('description');
            $table->integer('time_spent');
            $table->text('key_successes');
            $table->text('main_challenges');
            $table->text('plans_for_tomorrow');
            $table->timestamp('date_submitted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('end_of_day_reports');
    }
};
