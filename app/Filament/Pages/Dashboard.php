<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as PagesDashboard;
use App\Filament\Widgets\AlbumSongCountStats;
use App\Filament\Widgets\DashboardCalendar;
use Awcodes\Overlook\Widgets\OverlookWidget;
use Filament\Widgets\AccountWidget;

class Dashboard extends PagesDashboard {
    protected static ?string $navigationIcon = 'heroicon-s-home';

    public function getHeaderWidgetsColumns(): int | array {
        return [
            'md' => 4,
            'xl' => 3,
        ];
    }

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array {
        return [
            AccountWidget::class,
            AlbumSongCountStats::class,
            DashboardCalendar::class,
        ];
    }
}
