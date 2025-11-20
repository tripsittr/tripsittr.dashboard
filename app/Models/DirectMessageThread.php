<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DirectMessageThread extends Model
{
    protected $fillable = [
        'subject',
        'team_id', // optional, for team-based conversations
        'booking_request_id', // optional, for booking-scoped threads
    ];

    public function bookingRequest()
    {
        return $this->belongsTo(\App\Models\BookingRequest::class, 'booking_request_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(DirectMessageParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DirectMessage::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
