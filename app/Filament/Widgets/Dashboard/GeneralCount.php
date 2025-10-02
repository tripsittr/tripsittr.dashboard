<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Event;
use App\Models\InventoryItem;
use Filament\Facades\Filament;

class GeneralCount extends BaseWidget
{
    protected int | string | array $columnSpan = '1';

    public function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $activeTeamId = $tenant->id; // Assuming the active team is determined this way

        return [
            Stat::make('Events', Event::where('team_id', $activeTeamId)->count()),
        ];
    }
}
