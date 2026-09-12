<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'display_name',
        'bio',
        'location',
        'years_of_experience',
        'phone',
        'contact_email',
        'profile_photo',
        'availability_status',
        'average_rating',
        'reviews_count',
    ];

    /**
     * Hide phone and contact email by default for privacy until connection is active.
     */
    protected $hidden = [
        'phone',
        'contact_email',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'average_rating' => 'decimal:2',
        'reviews_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'professional_profile_skill');
    }

    public function educationProfile()
    {
        return $this->hasOne(EducationProfile::class);
    }

    public function connectionRequests()
    {
        return $this->hasMany(ConnectionRequest::class);
    }
}
