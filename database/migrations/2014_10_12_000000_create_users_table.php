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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
<<<<<<< HEAD
            $table->enum('role', ['company', 'job_seeker'])->default('job_seeker');
=======
            $table->enum('role',['company','job_seeker']);
            $table->boolean('is_verified')->default(false);
>>>>>>> bd87855d075935ca1bbc25d794b2acf764982de7
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
