<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PrivacyPolicy extends Page {
    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool {
        return false;
    }

    protected static string $view = 'filament.pages.privacy-policy';
}
