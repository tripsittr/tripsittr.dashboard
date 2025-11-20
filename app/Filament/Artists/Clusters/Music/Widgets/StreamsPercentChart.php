<?php

namespace App\Filament\Artists\Clusters\Music\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\SongAnalytics;
use Filament\Support\RawJs;

class StreamsPercentChart extends ApexChartWidget
{
    protected static ?string $chartId = 'songStreamsPercentChart';

    protected static ?string $heading = 'Streams (%)';

    protected static ?int $contentHeight = 300;

    protected int|string|array $columnSpan = 'auto';

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        $latest = SongAnalytics::when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->max('imported_at');

        if (! $latest) {
            return ['chart' => ['type' => 'bar', 'toolbar' => ['show' => false]], 'series' => [], 'xaxis' => ['categories' => []]];
        }

        $rows = SongAnalytics::where('imported_at', $latest)
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->orderByDesc('streams_pct')
            ->limit(12)
            ->get();

        $categories = $rows->map(fn($r) => mb_strwidth($r->name) > 30 ? mb_substr($r->name, 0, 27) . '...' : $r->name)->toArray();

        $streams_pct = $rows->map(fn($r) => (float) ($r->streams_pct ?? 0.0))->toArray();

        return [
            'chart' => ['type' => 'bar', 'height' => 280, 'toolbar' => ['show' => false]],
            'series' => [
                ['name' => 'Streams %', 'data' => $streams_pct],
            ],
            'plotOptions' => ['bar' => ['horizontal' => false, 'columnWidth' => '60%']],
            'xaxis' => ['categories' => $categories, 'labels' => ['rotate' => -30]],
            'yaxis' => [['title' => ['text' => 'Percent (%)']]],
            'colors' => ['#4CAF50'],
            'dataLabels' => ['enabled' => true],
            'tooltip' => ['shared' => true, 'intersect' => false],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            yaxis: [{
                labels: { formatter: function (val) { return val + '%'; } }
            }],
            dataLabels: {
                formatter: function (val) { return val + '%'; }
            },
            tooltip: {
                y: {
                    formatter: function (val) { return val + '%'; }
                }
            }
        }
        JS);
    }
}
