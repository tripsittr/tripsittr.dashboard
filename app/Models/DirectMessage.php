<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectMessage extends Model
{

    protected $fillable = [
        'thread_id',
        'sender_id',
        'sender_type', // User or VenueUser
        'encrypted_body',
        'attachment_path',
        'body', // virtual, for mutator
    ];

    /**
     * Set the message body (encrypt before saving).
     */
    public function setBodyAttribute($value)
    {
        $this->attributes['encrypted_body'] = encrypt($value);
    }

    /**
     * Get the decrypted message body.
     */
    public function getBodyAttribute()
    {
        return decrypt($this->attributes['encrypted_body']);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(DirectMessageThread::class, 'thread_id');
    }

    public function sender(): BelongsTo
    {
        return $this->morphTo();
    }
}
