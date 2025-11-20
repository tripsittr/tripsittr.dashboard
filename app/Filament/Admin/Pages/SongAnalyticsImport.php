<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SongAnalyticsImport extends Page
{
    protected static ?string $navigationLabel = 'Import Song Analytics';

    protected static ?string $navigationIcon = 'heroicon-s-arrow-up-tray';

    protected static string $view = 'filament.admin.pages.song-analytics-import';

    // Only allow users with the admin role to see this page
    public static function canView(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Use Spatie roles if available
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin');
        }

        // Fallback: allow any Filament-authenticated user
        return true;
    }
}
