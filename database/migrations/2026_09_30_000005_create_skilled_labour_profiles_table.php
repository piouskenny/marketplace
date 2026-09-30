<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skilled Labour specialisation table — analogous to education_profiles.
     *
     * This stores ONLY fields specific to skilled trade workers. All shared
     * base data (display_name, bio, location, phone, contact_email, skills,
     * availability_status, average_rating, reviews_count) lives on the
     * associated professional_profiles record.
     *
     * The presence of this record does NOT imply the user has the "professional"
     * talent classification. Classifications are tracked in talent_type_user only.
     */
    public function up(): void
    {
        Schema::create('skilled_labour_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')
                  ->unique()
                  ->constrained()
                  ->cascadeOnDelete();
            // Points to the specific trade subcategory (e.g. "Plumbers", "Electricians")
            $table->foreignId('trade_category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->boolean('is_certified')->default(false);
            $table->text('certification_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skilled_labour_profiles');
    }
};
