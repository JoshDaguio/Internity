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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('course_name')->unique();
            $table->timestamps();
        });
        DB::table('courses')->insert([
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('courses')->insert([
            'course_code' => 'BSCS',
            'course_name' => 'Bachelor of Science in Computer Science',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('courses')->insert([
            'course_code' => 'BMMA',
            'course_name' => 'Bachelor of Multimedia Arts',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};