<?php
namespace App\Filament\Index\Widgets;

use Filament\Widgets\Widget;

class DashboardMusicForArtists extends Widget {

    protected int | string | array $columnSpan = '1';
    protected static string $view = 'filament.widgets.dashboard-music-for-artists';
}
