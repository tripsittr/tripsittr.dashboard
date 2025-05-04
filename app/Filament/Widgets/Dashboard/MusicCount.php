<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Album;
use App\Models\Song;
use Filament\Facades\Filament;

class MusicCount extends BaseWidget
{
    protected int | string | array $columnSpan = '2';

    public function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $activeTeamId = $tenant->id; // Assuming the active team is determined this way

        return [
            Stat::make('Albums', Album::where('team_id', $activeTeamId)->count()),
            Stat::make('Songs', Song::where('team_id', $activeTeamId)->count()),
        ];
    }
}
