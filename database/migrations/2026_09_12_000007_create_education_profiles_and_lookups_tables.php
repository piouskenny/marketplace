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
        Schema::create('education_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('teaching_mode')->default('both'); // physical, online, both
            $table->text('qualifications')->nullable();
            $table->unsignedBigInteger('rate_min')->nullable(); // in kobo
            $table->unsignedBigInteger('rate_max')->nullable(); // in kobo
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('education_profile_subject', function (Blueprint $table) {
            $table->foreignId('education_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['education_profile_id', 'subject_id'], 'edu_profile_subj_primary');
        });

        Schema::create('education_profile_level', function (Blueprint $table) {
            $table->foreignId('education_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('education_level_id')->constrained()->cascadeOnDelete();
            $table->primary(['education_profile_id', 'education_level_id'], 'edu_profile_level_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_profile_level');
        Schema::dropIfExists('education_profile_subject');
        Schema::dropIfExists('education_levels');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('education_profiles');
    }
};
