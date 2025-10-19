<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\BlacklistedWordsTrait;
use Illuminate\Support\Facades\Storage;

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
        'bitrate_mode',
        'sample_rate',
        'codec',
        'format',
        'channels',
        'file_size',
        'mime_type',
        'track_number',
        'track_total',
        'disc_number',
        'disc_total',
        'replay_gain_track',
        'replay_gain_album',
        'md5_file',
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
        'user_id',
        'team_id',
        'album_id',
        'tenant_id',
        'raw_metadata',
    ];

    protected $casts = [
        // Ensure JSON arrays are automatically decoded; fallback to [] if null when accessed
        'primary_artists' => 'array',
        'featured_artists' => 'array',
        'producers' => 'array',
        'composers' => 'array',
        'current_owners' => 'array',
        'release_date' => 'datetime',
        'raw_metadata' => 'array',
        'waveform_data' => 'array',
    ];

    /**
     * Accessor helpers to always return an array (never null) for repeater-like attributes.
     */
    public function getPrimaryArtistsAttribute($value) { return $this->decodeRepeater($value); }
    public function getFeaturedArtistsAttribute($value) { return $this->decodeRepeater($value); }
    public function getProducersAttribute($value) { return $this->decodeRepeater($value); }
    public function getComposersAttribute($value) { return $this->decodeRepeater($value); }
    public function getCurrentOwnersAttribute($value) { return $this->decodeRepeater($value); }

    /**
     * Return a normalized storage-relative path for the audio file (strip leading slashes or storage/ prefix).
     */
    public function normalizedSongFile(): ?string
    {
        if (!$this->song_file) { return null; }
        $p = ltrim($this->song_file, '/');
        // Strip accidental 'storage/' prefix (public disk symlink)
        $p = preg_replace('#^storage/#','', $p);
        return $p;
    }

    /**
     * Compute the public URL to the song file if it exists on a configured disk.
     */
    public function getSongFileUrlAttribute(): ?string
    {
        $rel = $this->normalizedSongFile();
        if (!$rel) { return null; }
        try {
            return Storage::url($rel);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Attempt to resolve an absolute path to an existing audio file among known storage locations.
     */
    public function resolveSongAbsolutePath(): ?string
    {
        $rel = $this->normalizedSongFile();
        if (!$rel) { return null; }
        $candidates = [
            storage_path('app/public/'.$rel),
            public_path('storage/'.$rel),
            storage_path('app/private/'.$rel),
        ];
        foreach ($candidates as $c) {
            if (is_file($c)) { return $c; }
        }
        return null;
    }

    /**
     * Normalize a stored value into the Repeater expected array structure.
     * Accepts:
     *  - null => []
     *  - JSON string => decoded (expects array)
     *  - Plain string => wrap as [['name' => string]]
     *  - Already array => returned as-is (ensuring each scalar converted to ['name'=>...])
     */
    private function decodeRepeater($value): array {
        if ($value === null || $value === '') {
            return [];
        }

        // If already an array, normalize entries
        if (is_array($value)) {
            return $this->normalizeRepeaterArray($value);
        }

        // Attempt JSON decode if string
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->normalizeRepeaterArray($decoded);
            }
            // Fallback treat raw string as a single name entry
            return [[ 'name' => trim($value) ]];
        }

        return [];
    }

    private function normalizeRepeaterArray(array $value): array {
        // If array is list of scalars, convert to name entries
        if (array_is_list($value) && (empty($value) || !is_array($value[0] ?? null))) {
            return array_map(fn($v) => ['name' => is_scalar($v) ? (string)$v : ''], $value);
        }
        // If each element has a 'name' key, assume correct structure
        if (!empty($value) && array_key_exists('name', $value[0] ?? [])) {
            return $value;
        }
        // Mixed associative: map key/value into name entries
        $normalized = [];
        foreach ($value as $k => $v) {
            if (is_array($v) && array_key_exists('name', $v)) {
                $normalized[] = ['name' => (string)$v['name']];
            } elseif (is_scalar($v)) {
                $normalized[] = ['name' => (string)$v];
            }
        }
        return $normalized;
    }

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
