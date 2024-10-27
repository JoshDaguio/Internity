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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('evaluation_type', ['program', 'intern_student', 'intern_company']);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // This links to the user who created the evaluation
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // This links to the academic year
            $table->string('recipient_role')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade'); // FK for evaluations table
            $table->text('question_text');
            $table->enum('question_type', ['radio', 'long_text']);
            $table->timestamps();
        });

        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade'); // FK for evaluations table
            $table->foreignId('evaluator')->constrained('users')->onDelete('cascade'); // Reference to evaluator
            $table->foreignId('evaluatee')->nullable()->constrained('users')->onDelete('cascade'); // Reference to evaluatee
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade'); // FK for questions table
            $table->string('response_text')->nullable(); // For long text answers
            $table->integer('response_value')->nullable(); // For radio button answers (1-4)
            $table->string('supervisor')->nullable(); // Supervisor's name if needed
            $table->timestamps();
        });

        Schema::create('evaluation_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade'); // FK for evaluations table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // FK for users table
            $table->boolean('is_answered')->default(false); // Track if answered
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_recipients');
        Schema::dropIfExists('responses');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('evaluations');
    }
};
