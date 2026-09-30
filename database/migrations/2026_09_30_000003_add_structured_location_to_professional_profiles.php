<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 4 structured location columns to professional_profiles.
     *
     * These sit alongside the existing free-text `location` column (preserved for
     * backward compatibility). All talent types (Professional, Teacher, Skilled Labour)
     * obtain their general service location from professional_profiles — one source of truth.
     *
     * Privacy tiers:
     *   location_state + location_city     → shown publicly on profile cards & discovery
     *   location_neighbourhood             → shown on full profile page
     *   location_landmark                  → shown only post-connection (descriptive, not precise)
     */
    public function up(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->string('location_state', 100)->nullable()->after('location');
            $table->string('location_city', 100)->nullable()->after('location_state');
            $table->string('location_neighbourhood', 100)->nullable()->after('location_city');
            $table->text('location_landmark')->nullable()->after('location_neighbourhood');
        });
    }

    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['location_state', 'location_city', 'location_neighbourhood', 'location_landmark']);
        });
    }
};
