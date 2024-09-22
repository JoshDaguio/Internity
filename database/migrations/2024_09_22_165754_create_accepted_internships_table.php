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
        Schema::create('accepted_internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users'); // Refers to the student user
            $table->foreignId('company_id')->constrained('users'); // Refers to the company user
            $table->foreignId('job_id')->constrained('jobs'); // Refers to the job posting
            $table->json('schedule'); // Stores scheduled days (JSON format)
            $table->enum('work_type', ['Remote', 'On-site', 'Hybrid']); // Stores the work type (Remote, On-site, Hybrid)
            $table->time('start_time'); // Stores the internship start time
            $table->time('end_time'); // Stores the internship end time
            $table->date('start_date'); // The start date of the internship
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accepted_internships');
    }
};
