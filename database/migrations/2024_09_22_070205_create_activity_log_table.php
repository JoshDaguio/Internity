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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users'); // The admin who performed the action
            $table->string('action'); // The action performed, e.g., 'created', 'updated', 'deleted'
            $table->string('target'); // The target of the action, e.g., 'student', 'course'
            $table->json('changes')->nullable(); // Store changes for updates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
