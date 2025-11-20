<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectMessageParticipant extends Model
{
    protected $fillable = [
        'thread_id',
        'participant_id',
        'participant_type', // User or VenueUser
        'last_read_at',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(DirectMessageThread::class, 'thread_id');
    }

    public function participant(): BelongsTo
    {
        return $this->morphTo();
    }
}
