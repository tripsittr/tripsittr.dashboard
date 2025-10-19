<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Settings
{
    protected static string $cacheKey = 'app_settings_cache_v1';
    protected static bool $loaded = false;
    protected static array $items = [];

    protected static function load(): void
    {
        if (static::$loaded) {
            return;
        }
        try {
            static::$items = Cache::rememberForever(static::$cacheKey, function () {
                if (! Schema::hasTable('app_settings')) {
                    return [];
                }
                return AppSetting::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            static::$items = [];
        }
        static::$loaded = true;
    }

    public static function get(string $key, $default = null): mixed
    {
        static::load();
        return static::$items[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        AppSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$items[$key] = $value;
        Cache::forget(static::$cacheKey);
        static::$loaded = false;
    }

    public static function all(): array
    {
        static::load();
        return static::$items;
    }
}
