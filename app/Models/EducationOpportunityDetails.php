<?php

namespace App\Models;

use App\Enums\TeachingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationOpportunityDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'subject_id',
        'education_level_id',
        'teaching_mode',
        'schedule_notes',
        'student_notes',
    ];

    protected $casts = [
        'teaching_mode' => TeachingMode::class,
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }
}
