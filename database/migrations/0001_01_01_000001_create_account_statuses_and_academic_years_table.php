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
        Schema::create('account_statuses', function (Blueprint $table) {
            $table->id()->primary(); // Set id as primary key and integer
            $table->string('status');
            $table->timestamps();
        });

        // Insert predefined statuses
        DB::table('account_statuses')->insert([
            ['id' => 1, 'status' => 'Active'],
            ['id' => 2, 'status' => 'Inactive'],
            ['id' => 3, 'status' => 'Pending'],
        ]);

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('start_year');
            $table->string('end_year');
            $table->boolean('is_current')->default(false); // To mark the current academic year
            $table->timestamps();
        });

        // Seed initial data (optional)
        DB::table('academic_years')->insert([
            ['start_year' => '2024', 'end_year' => '2025', 'is_current' => true],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_statuses');
        Schema::dropIfExists('academic_years');
    }
};
