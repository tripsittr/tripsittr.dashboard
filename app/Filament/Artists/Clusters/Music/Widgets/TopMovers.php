<?php

namespace App\Filament\Artists\Clusters\Music\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Widgets\Widget;
use App\Models\SongAnalytics;

class TopMovers extends Widget
{
    protected static string $view = 'filament.clusters.music.widgets.top-movers';

    protected int|string|array $columnSpan = 'auto';

    public function getData(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        $latest = SongAnalytics::when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->max('imported_at');

        if (! $latest) {
            return ['gainers' => [], 'losers' => []];
        }

        $rows = SongAnalytics::where('imported_at', $latest)
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->get();

        $gainers = $rows->sortByDesc(fn($r) => $r->streams_change ?? 0)->take(5)->values();
        $losers = $rows->sortBy(fn($r) => $r->streams_change ?? 0)->take(5)->values();

        return [
            'gainers' => $gainers,
            'losers' => $losers,
        ];
    }
}
