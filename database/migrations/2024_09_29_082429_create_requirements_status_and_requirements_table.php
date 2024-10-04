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
        Schema::create('requirement_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status'); // Status name
            $table->timestamps();
        });

        // Insert default statuses
        DB::table('requirement_statuses')->insert([
            ['status' => 'To Review'],
            ['status' => 'Accepted'],
            ['status' => 'Rejected'],
        ]);

        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users'); // Link to the users table (student)
            $table->string('waiver_form')->nullable(); // Waiver form file path
            $table->string('medical_certificate')->nullable(); // Medical certificate file path
            $table->string('endorsement_letter')->nullable(); // Placeholder for endorsement letter (Step 2)

            $table->foreignId('waiver_status_id')->default(1)->constrained('requirement_statuses'); // Default to 'To Review'
            $table->foreignId('medical_status_id')->default(1)->constrained('requirement_statuses'); // Default to 'To Review'

            $table->foreignId('status_id')->constrained('requirement_statuses'); // Link to the requirement_statuses table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
        Schema::dropIfExists('requirement_statuses');
    }
};
