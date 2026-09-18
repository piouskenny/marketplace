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

    /**
     * Determine whether a given user can see unmasked private contact information.
     */
    public function canSeeContactDetails(?User $viewer): bool
    {
        if (!$viewer) {
            return false;
        }

        // Profile owner can always view their own contact details
        if ((int) $viewer->id === (int) $this->user_id) {
            return true;
        }

        // Check if there is an active Connected request between viewer and profile owner
        return ConnectionRequest::where(function ($q) use ($viewer) {
                $q->where('initiator_id', $viewer->id)
                  ->where('recipient_id', $this->user_id);
            })->orWhere(function ($q) use ($viewer) {
                $q->where('initiator_id', $this->user_id)
                  ->where('recipient_id', $viewer->id);
            })
            ->where(function ($q) {
                $q->where('status', \App\Enums\ConnectionStatus::Connected)
                  ->orWhere('status', 'connected');
            })
            ->exists();
    }

    /**
     * Return full phone number if authorized, or a masked phone string otherwise.
     */
    public function getDisplayPhone(?User $viewer): string
    {
        $rawPhone = $this->phone ?? ($this->user ? $this->user->phone : null);
        if (empty($rawPhone)) {
            return 'Not provided';
        }

        if ($this->canSeeContactDetails($viewer)) {
            return $rawPhone;
        }

        $len = strlen($rawPhone);
        if ($len <= 5) {
            return '••••••••';
        }

        return substr($rawPhone, 0, 4) . ' •••• ' . substr($rawPhone, -2);
    }

    /**
     * Return full email if authorized, or a masked email string otherwise.
     */
    public function getDisplayEmail(?User $viewer): string
    {
        $rawEmail = $this->contact_email ?? ($this->user ? $this->user->email : null);
        if (empty($rawEmail)) {
            return 'Not provided';
        }

        if ($this->canSeeContactDetails($viewer)) {
            return $rawEmail;
        }

        $parts = explode('@', $rawEmail);
        $namePart = $parts[0];
        $domain = $parts[1] ?? 'email.com';

        $maskedName = strlen($namePart) > 2 
            ? substr($namePart, 0, 2) . '••••'
            : '••';

        return $maskedName . '@' . $domain;
    }
}

