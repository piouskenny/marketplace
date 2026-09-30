<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add database indexes for fast structured location search and completed talent classification queries.
     */
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->index('location_state', 'idx_prof_profiles_loc_state');
            $table->index('location_city', 'idx_prof_profiles_loc_city');
            $table->index('location_neighbourhood', 'idx_prof_profiles_loc_neighbourhood');
        });

        Schema::table('talent_type_user', function (Blueprint $table) {
            $table->index('completed_at', 'idx_ttu_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropIndex('idx_prof_profiles_loc_state');
            $table->dropIndex('idx_prof_profiles_loc_city');
            $table->dropIndex('idx_prof_profiles_loc_neighbourhood');
        });

        Schema::table('talent_type_user', function (Blueprint $table) {
            $table->dropIndex('idx_ttu_completed_at');
        });
    }
};
