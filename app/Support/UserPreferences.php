<?php

namespace App\Support;

use App\Models\UserSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class UserPreferences
{
    protected static string $cachePrefix = 'user_prefs_v1_';

    public static function get(string $key, $default = null, ?int $userId = null)
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return $default;
        // If table not migrated yet, fall back immediately.
        if (! Schema::hasTable('user_settings')) {
            return $default;
        }
        $cacheKey = self::$cachePrefix.$userId.'_'.$key;
        return Cache::remember($cacheKey, 3600, function () use ($userId, $key, $default) {
            try {
                $row = UserSetting::where('user_id',$userId)->where('key',$key)->first();
                return $row?->value ?? $default;
            } catch (\Throwable $e) {
                return $default; // fail-safe if migration mid-run
            }
        });
    }

    public static function set(string $key, $value, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return;
        if (! Schema::hasTable('user_settings')) {
            return; // can't persist yet
        }
        try {
            UserSetting::updateOrCreate(
                ['user_id' => $userId, 'key' => $key],
                ['value' => $value]
            );
            Cache::forget(self::$cachePrefix.$userId.'_'.$key);
        } catch (\Throwable $e) {
            // swallow - preference persistence is non-critical
        }
    }
}
