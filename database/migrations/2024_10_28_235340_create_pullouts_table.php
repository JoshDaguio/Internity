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
        Schema::create('pullouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Tracks the admin who created the pullout request
            $table->date('pullout_date');
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->text('excuse_detail'); // Reason for excusing the student
            $table->text('company_remark')->nullable(); // Company's response remark
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // Relate to academic year
            $table->timestamps();
        });

        Schema::create('pullout_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pullout_id')->constrained('pullouts')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pullout_students');
        Schema::dropIfExists('pullouts');
    }
};
