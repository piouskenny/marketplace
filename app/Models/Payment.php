<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'connection_request_id',
        'reference',
        'provider',
        'amount',
        'currency',
        'status',
        'provider_reference',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function connectionRequest()
    {
        return $this->belongsTo(ConnectionRequest::class);
    }
}
