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
        //skill tags
        Schema::create('skill_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Name of the skill tag
            $table->timestamps();
        });

        //for user profile
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('id_number')->nullable()->unique();
            $table->text('about')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('cv_file_path')->nullable(); // For Curriculum Vitae file upload
            $table->string('profile_picture')->nullable(); // For Profile Picture upload
            $table->timestamps();
        });

        // profile links
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->string('link_name');
            $table->string('link_url');
            $table->timestamps();
        });

        //users account
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('status_id')->constrained('account_statuses');
            $table->foreignId('course_id')->nullable()->constrained('courses');
            $table->foreignId('profile_id')->nullable()->constrained('profiles');
            $table->rememberToken();
            $table->timestamps();
        });

        //Set up of Super Admin Account and Profile

        DB::table('profiles')->insert([
            'first_name' => 'Super',
            'last_name' => 'Admin',
        ]);

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'internity.ccs@gmail.com',
            'password' => Hash::make('12345678'), // Hash the password
            'role_id' => 1, // Super Admin
            'status_id' => 1, // Active
            'profile_id' => 1, // First Profile Created Above
            'created_at' => now(),
            'updated_at' => now()
        ]);


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('skill_tags');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('links');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        Schema::enableForeignKeyConstraints();
    }
};
