<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create application_statuses table
        Schema::create('application_statuses', function (Blueprint $table) {
            $table->id()->primary(); // Set id as primary key and integer
            $table->string('status');
            $table->timestamps();
        });

        // Insert predefined statuses
        DB::table('application_statuses')->insert([
            ['id' => 1, 'status' => 'To Review'],
            ['id' => 2, 'status' => 'Accepted'],
            ['id' => 3, 'status' => 'Rejected'],
        ]);

        // Create applications table
        Schema::create('applications', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('job_id')->constrained('jobs');
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('status_id')->constrained('application_statuses');
            $table->timestamp('date_posted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('application_statuses');
    }
};
