<?php

namespace App\Filament\Artists\Clusters\Music\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\SongAnalytics;
use Filament\Support\RawJs;

class SongAnalyticsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'songAnalyticsChart';

    protected static ?string $heading = 'Song Analytics (latest import)';

    protected static ?int $contentHeight = 320;

    protected int|string|array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        $latest = SongAnalytics::when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->max('imported_at');

        if (! $latest) {
            return [
                'chart' => ['type' => 'bar', 'height' => 260, 'toolbar' => ['show' => false]],
                'series' => [],
                'xaxis' => ['categories' => []],
            ];
        }

        $rows = SongAnalytics::where('imported_at', $latest)
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->orderByDesc('streams')
            ->limit(12)
            ->get();

        $categories = $rows->map(fn($r) => mb_strwidth($r->name) > 30 ? mb_substr($r->name, 0, 27) . '...' : $r->name)->toArray();

        $streams_change = $rows->map(fn($r) => $r->streams_change ?? 0)->toArray();
        $streams_pct = $rows->map(fn($r) => $r->streams_pct ?? 0.0)->toArray();
        $streams_change_pct = $rows->map(fn($r) => $r->streams_change_pct ?? 0.0)->toArray();

        return [
            // Regular vertical grouped bar chart (all series as bars) for a classic bar chart look
            'chart' => ['type' => 'bar', 'height' => 380, 'toolbar' => ['show' => false]],
            'series' => [
                ['name' => 'Streams change', 'data' => $streams_change],
                ['name' => 'Streams %', 'data' => $streams_pct],
                ['name' => 'Streams change %', 'data' => $streams_change_pct],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '50%',
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'xaxis' => ['categories' => $categories, 'labels' => ['rotate' => -30]],
            // Provide basic yaxis config here (no PHP closures). JS formatters are added via extraJsOptions().
            'yaxis' => [
                ['title' => ['text' => 'Value']],
            ],
            'legend' => ['position' => 'top', 'horizontalAlign' => 'right'],
            'colors' => ['#C75D5D', '#4CAF50', '#2196F3'],
            'tooltip' => ['shared' => true, 'intersect' => false],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            yaxis: [
                {},
                {
                    opposite: true,
                    labels: {
                        formatter: function (val) { return val + '%'; }
                    }
                }
            ],
            tooltip: {
                y: {
                    formatter: function (val, opts) { return val; }
                }
            }
        }
        JS);
    }
}
