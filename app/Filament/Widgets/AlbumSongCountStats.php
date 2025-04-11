<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Album;
use App\Models\InventoryItem;
use App\Models\Song;
use Filament\Facades\Filament;

class AlbumSongCountStats extends BaseWidget {

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array {
        $tenant = Filament::getTenant();
        $activeTeamId = $tenant->id; // Assuming the active team is determined this way

        return [
            Stat::make('Albums', Album::where('team_id', $activeTeamId)->count()),
            Stat::make('Songs', Song::where('team_id', $activeTeamId)->count()),
            Stat::make('Inventory', InventoryItem::where('team_id', $activeTeamId)->count()),
        ];
    }
}
