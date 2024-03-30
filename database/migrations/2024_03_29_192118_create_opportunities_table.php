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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("title");
            $table->text("body");
            $table->string("file");
            $table->string("location");
            $table->enum('job_type', ['full-time', 'part-time', 'contract', 'temporary', 'volunteer']);
            $table->enum('work-place_type', ['on-site', 'hybrid', 'remote']);
            $table->integer('job_hours');
            $table->text('qualifications');
            $table->text('skills_req');
            $table->float('salary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
