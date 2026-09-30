<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'location',
        'avatar',
        'onboarding_completed',
        'onboarding_intent',
        'talent_onboarding_started_at',
        'job_alerts_enabled',
        'password',
    ];

    /**
     * Get the avatar URL or null.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        if (str_starts_with($this->avatar, 'images/')) {
            return asset($this->avatar);
        }

        return asset('storage/' . $this->avatar);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'             => 'datetime',
            'password'                       => 'hashed',
            'onboarding_completed'           => 'boolean',
            'job_alerts_enabled'             => 'boolean',
            'talent_onboarding_started_at'   => 'datetime',
        ];
    }

    /**
     * Check if user has completed their onboarding/profile setup.
     */
    public function isProfileComplete(): bool
    {
        return (bool) $this->onboarding_completed;
    }

    public function professionalProfile()
    {
        return $this->hasOne(ProfessionalProfile::class);
    }

    /**
     * Talent classifications this user holds (professional, teacher, skilled_labour).
     * One user can hold zero, one, or multiple classifications simultaneously.
     */
    public function talentTypes(): BelongsToMany
    {
        return $this->belongsToMany(TalentType::class, 'talent_type_user')
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    // ─── Classification helpers ─────────────────────────────────────────────
    // Call these after eager-loading talentTypes to avoid extra DB queries.

    public function isProfessional(): bool
    {
        return $this->relationLoaded('talentTypes')
            ? $this->talentTypes->contains('slug', 'professional')
            : $this->talentTypes()->where('slug', 'professional')->exists();
    }

    public function isTeacher(): bool
    {
        return $this->relationLoaded('talentTypes')
            ? $this->talentTypes->contains('slug', 'teacher')
            : $this->talentTypes()->where('slug', 'teacher')->exists();
    }

    public function isSkilledLabour(): bool
    {
        return $this->relationLoaded('talentTypes')
            ? $this->talentTypes->contains('slug', 'skilled_labour')
            : $this->talentTypes()->where('slug', 'skilled_labour')->exists();
    }

    /**
     * True if the user holds at least one talent classification.
     */
    public function isAnyTalent(): bool
    {
        return $this->relationLoaded('talentTypes')
            ? $this->talentTypes->isNotEmpty()
            : $this->talentTypes()->exists();
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function initiatedConnectionRequests()
    {
        return $this->hasMany(ConnectionRequest::class, 'initiator_id');
    }

    public function receivedConnectionRequests()
    {
        return $this->hasMany(ConnectionRequest::class, 'recipient_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }
}

