<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Skilled Labour specialisation profile.
 *
 * Analogous to EducationProfile — this is NOT a standalone profile.
 * It extends the shared base ProfessionalProfile with trade-specific data.
 *
 * All common fields (display_name, bio, location, phone, contact_email,
 * profile_photo, skills, availability_status, average_rating, reviews_count)
 * live on the associated ProfessionalProfile record.
 *
 * The presence of this record does NOT mean the user has the "Professional"
 * talent classification. Classifications are tracked in talent_type_user only.
 */
class SkilledLabourProfile extends Model
{
    protected $fillable = [
        'professional_profile_id',
        'trade_category_id',
        'is_certified',
        'certification_notes',
    ];

    protected $casts = [
        'is_certified' => 'boolean',
    ];

    /**
     * The shared base profile that provides all common display/contact data.
     */
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    /**
     * The specific trade category (e.g. "Plumbers", "Electricians").
     * Typically a child category under "Home & Technical Services".
     */
    public function tradeCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'trade_category_id');
    }
}
