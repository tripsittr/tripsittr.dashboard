<?php

namespace App\Filament\Index\Pages;

use App\Filament\Index\Widgets\Dashboard\GeneralCount;
use App\Filament\Index\Widgets\Dashboard\MusicCount;
use App\Filament\Index\Widgets\DashboardCalendar;
use App\Filament\Index\Widgets\DashboardMusicForArtists;
use App\Filament\Index\Widgets\SpacerWidget;
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
            DashboardMusicForArtists::class,
            SpacerWidget::class,
            AccountWidget::class,
            MusicCount::class,
            GeneralCount::class,
            DashboardCalendar::class,
        ];
    }
}
