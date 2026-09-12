<?php

namespace App\Models;

use App\Enums\TeachingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_profile_id',
        'teaching_mode',
        'qualifications',
        'rate_min',
        'rate_max',
    ];

    protected $casts = [
        'teaching_mode' => TeachingMode::class,
        'rate_min' => 'integer',
        'rate_max' => 'integer',
    ];

    public function professionalProfile()
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'education_profile_subject');
    }

    public function educationLevels()
    {
        return $this->belongsToMany(EducationLevel::class, 'education_profile_level');
    }
}
