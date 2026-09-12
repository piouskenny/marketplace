<?php

namespace App\Models;

use App\Enums\OpportunityStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'opportunity_type',
        'budget_min',
        'budget_max',
        'status',
        'application_deadline',
    ];

    protected $casts = [
        'status' => OpportunityStatus::class,
        'budget_min' => 'integer',
        'budget_max' => 'integer',
        'application_deadline' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function educationDetails()
    {
        return $this->hasOne(EducationOpportunityDetails::class);
    }

    public function connectionRequests()
    {
        return $this->hasMany(ConnectionRequest::class);
    }
}
