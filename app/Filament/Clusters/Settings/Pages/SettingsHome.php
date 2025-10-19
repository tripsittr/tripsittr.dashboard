<?php

namespace App\Filament\Clusters\Settings\Pages;

use Filament\Pages\Page;

class SettingsHome extends Page
{
    protected static string $view = 'filament.settings.home';

    protected static ?string $cluster = \App\Filament\Clusters\Settings\Settings::class;

    // Remove icon & label to avoid appearing in navigation (kept null intentionally)
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = null;

    public static function shouldRegisterNavigation(): bool
    {
        // Explicitly prevent registration in the cluster nav.
        return false;
    }
}
