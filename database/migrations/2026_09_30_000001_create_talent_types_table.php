<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();       // professional | teacher | skilled_labour
            $table->string('label', 100);               // Professional | Teacher | Skilled Labour Worker
            $table->string('icon', 100)->nullable();    // heroicon slug for UI
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_types');
    }
};
