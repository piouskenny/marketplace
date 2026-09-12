<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function professionalProfiles()
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'professional_profile_skill');
    }
}
