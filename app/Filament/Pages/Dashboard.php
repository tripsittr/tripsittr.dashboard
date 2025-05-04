<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as PagesDashboard;
use App\Filament\Widgets\DashboardCalendar;
use App\Filament\Widgets\DashboardMusicForArtists;
use App\Filament\Widgets\Dashboard\GeneralCount;
use App\Filament\Widgets\Dashboard\MusicCount;
use App\Filament\Widgets\SpacerWidget;
use Filament\Widgets\AccountWidget;

class Dashboard extends PagesDashboard {
    protected static ?string $navigationIcon = 'heroicon-s-home';

    public function getHeaderWidgetsColumns(): int | array {
        return 4;
    }

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array {
        return [
            AccountWidget::class,
            SpacerWidget::class,
            SpacerWidget::class,
            DashboardMusicForArtists::class,
            MusicCount::class,
            GeneralCount::class,
            DashboardCalendar::class,
        ];
    }
}
