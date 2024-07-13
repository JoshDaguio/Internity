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
        Schema::create('account_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status');
        });
        
        // Insert predefined statuses
        DB::table('account_statuses')->insert([
            ['id' => 1, 'status' => 'Active'],
            ['id' => 2, 'status' => 'Inactive'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_statuses');
    }
};
