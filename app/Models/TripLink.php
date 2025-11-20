<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TripLink extends Model
{
    protected $table = 'trip_links';

    protected $guarded = ['id'];

    protected $casts = [
        'design' => 'array',
        'links' => 'array',
        'social' => 'array',
        'layout' => 'array',
        'fonts' => 'array',
        'published' => 'boolean',
    ];

    public static function forTeam($teamId)
    {
        return static::firstOrCreate(['team_id' => $teamId], ['slug' => self::generateSlug($teamId)]);
    }

    public static function generateSlug($teamId)
    {
        // fallback: use team id when no better slug available; front-end should update slug
        return 'team-'.$teamId;
    }

    protected static function booted()
    {
        static::creating(function (self $model) {
            if (! empty($model->slug)) {
                return;
            }

            $base = Str::slug($model->title ?? null ?: ('team-'.($model->team_id ?? '0')));
            $candidate = $base ?: 'team-'.($model->team_id ?? '0');
            $i = 1;
            while (static::where('slug', $candidate)->exists()) {
                $candidate = ($base ?: 'team-'.($model->team_id ?? '0')).'-'.$i++;
            }

            $model->slug = $candidate;
        });
    }

    /**
     * Normalize and sanitize the design payload before saving.
     * Ensures color values are normalized to hex (when possible) and
     * that any custom CSS is run through the application's sanitizer.
     * This protects renderers from malformed values and provides a
     * consistent shape for the `design` JSON column.
     *
     * @param  array|null  $value
     * @return void
     */
    public function setDesignAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['design'] = json_encode([]);

            return;
        }

        // ensure we have an array
        $design = is_array($value) ? $value : (array) $value;

        // Normalize common color keys
        foreach (['background_color', 'text_color', 'accent_color'] as $k) {
            if (array_key_exists($k, $design)) {
                $design[$k] = $this->normalizeHex((string) ($design[$k] ?? ''));
            }
        }

        // Normalize numeric presentation keys
        if (array_key_exists('avatar_size', $design)) {
            $design['avatar_size'] = max(24, min(1200, intval($design['avatar_size'] ?? 120)));
        }

        if (array_key_exists('hero_height', $design)) {
            $design['hero_height'] = max(80, min(2000, intval($design['hero_height'] ?? 200)));
        }

        // Sanitize custom_css if present
        if (! empty($design['custom_css'])) {
            $design['custom_css'] = \App\Support\CssSanitizer::sanitize((string) $design['custom_css']);
        }

        $this->attributes['design'] = json_encode($design);
    }

    /**
     * Normalize a color string to a 3- or 6-digit hex with a leading '#'.
     * Falls back to empty string when value is invalid or not provided.
     */
    protected function normalizeHex(string $hex): string
    {
        $hex = trim($hex);
        if ($hex === '') {
            return '';
        }

        // allow leading '#' or not
        if (! str_starts_with($hex, '#')) {
            $hex = '#'.$hex;
        }

        // compact 3 or 6 hex digits
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hex)) {
            return strtolower($hex);
        }

        // attempt to parse rgb() or rgba(...) -> convert to hex
        if (preg_match('/^rgba?\s*\(([^)]+)\)$/i', $hex, $m)) {
            $parts = array_map('trim', explode(',', $m[1]));
            if (count($parts) >= 3) {
                $r = intval(preg_replace('/[^0-9]/', '', $parts[0]));
                $g = intval(preg_replace('/[^0-9]/', '', $parts[1]));
                $b = intval(preg_replace('/[^0-9]/', '', $parts[2]));

                return sprintf('#%02x%02x%02x', max(0, min(255, $r)), max(0, min(255, $g)), max(0, min(255, $b)));
            }
        }

        return '';
    }

    /**
     * Convenience accessors to get normalized color values from the design array.
     */
    public function getBackgroundColorAttribute(): string
    {
        return $this->design['background_color'] ?? '';
    }

    public function getTextColorAttribute(): string
    {
        return $this->design['text_color'] ?? '';
    }

    public function getAccentColorAttribute(): string
    {
        return $this->design['accent_color'] ?? '#f87171';
    }
}
