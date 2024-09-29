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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->string('violation'); // Name of the violation
            $table->integer('penalty_hours')->nullable(); // Fixed penalty hours, can be null for conditional violations
            $table->string('conditions')->nullable(); // Conditions (e.g., "1 hour for every 10 minutes late")
            $table->string('penalty_type')->default('fixed'); // Penalty type: fixed or conditional
            $table->softDeletes();
            $table->timestamps();
        });

        // Insert penalty data with fixed and conditional penalties
        DB::table('penalties')->insert([
            [
                'violation' => 'Unexcused Absence',
                'penalty_hours' => 16, // 2 days
                'conditions' => null,
                'penalty_type' => 'fixed',
            ],
            [
                'violation' => 'Excused Absence',
                'penalty_hours' => 8, // 1 day
                'conditions' => null,
                'penalty_type' => 'fixed',
            ],
            [
                'violation' => 'Tardiness (Less than 30 minutes)',
                'penalty_hours' => null,
                'conditions' => '1 hour for every 10 minutes',
                'penalty_type' => 'conditional',
            ],
            [
                'violation' => 'Tardiness (More than 1 hour but less than 4 hours)',
                'penalty_hours' => 8, // 1 day
                'conditions' => null,
                'penalty_type' => 'fixed',
            ],
            [
                'violation' => 'AWOL and Out of Post',
                'penalty_hours' => 24, // 3 days
                'conditions' => null,
                'penalty_type' => 'fixed',
            ],
            [
                'violation' => 'Alcohol Intoxication and Gambling within Company Premises',
                'penalty_hours' => 120, // 3 weeks (40 hours/week)
                'conditions' => null,
                'penalty_type' => 'fixed',
            ],
            [
                'violation' => 'Insubordination',
                'penalty_hours' => 56, // 7 days
                'conditions' => null,
                'penalty_type' => 'fixed',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
