<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track when a user first begins talent onboarding (independent of completion).
     *
     * NULL  = user has not started any talent classification onboarding
     * value = timestamp when the user first selected a talent classification
     *
     * Used by the dashboard profile-completion widget to distinguish between
     * "chose to skip talent setup" and "has not yet reached that step".
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('talent_onboarding_started_at')
                  ->nullable()
                  ->after('onboarding_intent');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('talent_onboarding_started_at');
        });
    }
};
