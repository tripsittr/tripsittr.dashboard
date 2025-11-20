<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRequest extends Model
{
    protected $fillable = [
        'artist_id',
        'venue_id',
        'status',
        'start_time',
        'end_time',
        'notes',
        'setlist',
    ];

    protected $casts = [
        'setlist' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
