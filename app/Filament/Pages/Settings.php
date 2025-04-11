<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Settings extends Page {
    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.settings';
}
