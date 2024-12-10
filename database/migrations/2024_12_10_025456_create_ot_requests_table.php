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
        Schema::create('ot_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users'); 
            $table->date('date_request'); // Date the student is requesting OT
            $table->time('ot_start_time'); // OT start time
            $table->time('ot_end_time'); // OT end time
            $table->text('details'); // Details of the request
            $table->string('proof_file_path'); // File path for proof
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status of the request
            $table->text('remarks')->nullable(); // Remarks for rejection/approval
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ot_requests');
    }
};
