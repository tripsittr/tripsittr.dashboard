<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends PagesDashboard
{
    protected static ?string $navigationIcon = 'heroicon-s-home';

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
        ];
    }
}
