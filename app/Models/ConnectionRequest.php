<?php

namespace App\Models;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConnectionRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'initiator_id',
        'recipient_id',
        'professional_profile_id',
        'opportunity_id',
        'type',
        'status',
        'initial_message',
        'accepted_at',
        'connected_at',
        'declined_at',
        'cancelled_at',
    ];

    protected $casts = [
        'type' => ConnectionType::class,
        'status' => ConnectionStatus::class,
        'accepted_at' => 'datetime',
        'connected_at' => 'datetime',
        'declined_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function professionalProfile()
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
