<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'meta',
        'facebook_page_id',
        'instagram_business_account_id',
    ];

    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenience factory for the Instagram service when this account is an Instagram-linked provider.
     */
    public function instagramService(): ?\App\Services\Social\InstagramService
    {
        if (!in_array($this->provider, ['facebook', 'instagram'])) {
            return null;
        }

        return new \App\Services\Social\InstagramService($this);
    }
}
