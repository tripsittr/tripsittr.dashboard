<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Partners extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.partners';

    protected static bool $shouldRegisterNavigation = false;
}
