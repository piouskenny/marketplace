<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_type_user', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('talent_type_id')
                  ->constrained()
                  ->cascadeOnDelete();
            // NULL  = classification chosen but onboarding sub-form not yet completed
            // value = timestamp when the user fully completed this classification's onboarding
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->primary(['user_id', 'talent_type_id'], 'talent_type_user_primary');
            $table->index('user_id');
            $table->index('talent_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_type_user');
    }
};
