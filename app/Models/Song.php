<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BlacklistedWordsTrait;

class Song extends Model {
    use HasFactory;
    use BlacklistedWordsTrait;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'song_file',
        'waveform_data',
        'genre',
        'subgenre',
        'duration',
        'bitrate',
        'sample_rate',
        'codec',
        'format',
        'channels',
        'primary_artists',
        'featured_artists',
        'producers',
        'composers',
        'current_owners',
        'isrc',
        'upc',
        'label_name',
        'release_date',
        'status',
        'visibility',
        'distribution_status',
        'copyright_notice',
        'performance_rights_org',
        'license_type',
        'artwork',
        'ownable_id',
        'ownable_type',
        'album_id',
        'tenant_id',
    ];

    protected $casts = [
        'primary_artists' => 'array',
        'featured_artists' => 'array',
        'producers' => 'array',
        'composers' => 'array',
        'current_owners' => 'array',
        'release_date' => 'datetime',
    ];

    public function album() {
        return $this->belongsTo(Album::class);
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function scopeForTenant($query, $tenantId) {
        return $query->where('tenant_id', $tenantId);
    }

    // Add logging to the model's boot method
    protected static function boot() {
        parent::boot();

        static::retrieved(function ($song) {
            Log::info('Song retrieved:', [
                'primary_artists' => $song->primary_artists,
                'featured_artists' => $song->featured_artists,
                'producers' => $song->producers,
                'composers' => $song->composers,
            ]);
        });
    }

    protected static function booted() {
    }

    public function getBlacklistedFields(): array {
        return array_merge($this->fillable, ['name', 'description']);
    }
}
