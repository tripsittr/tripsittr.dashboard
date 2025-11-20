<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'request_id',
        'artist_id',
        'venue_id',
        'confirmed_at',
        'setlist',
        'payment_status',
    ];

    protected $casts = [
        'setlist' => 'array',
        'confirmed_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class, 'request_id');
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
