<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function educationProfiles()
    {
        return $this->belongsToMany(EducationProfile::class, 'education_profile_subject');
    }
}
