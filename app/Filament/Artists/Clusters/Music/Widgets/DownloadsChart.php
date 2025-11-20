<?php

namespace App\Filament\Artists\Clusters\Music\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\SongAnalytics;

class DownloadsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'songDownloadsChart';

    protected static ?string $heading = 'Downloads (count)';

    protected static ?int $contentHeight = 300;

    protected int|string|array $columnSpan = 'full';

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
            ->orderByDesc('downloads')
            ->limit(12)
            ->get();

        $categories = $rows->map(fn($r) => mb_strwidth($r->name) > 30 ? mb_substr($r->name, 0, 27) . '...' : $r->name)->toArray();

        $downloads = $rows->map(fn($r) => (int) ($r->downloads ?? 0))->toArray();

        return [
            'chart' => ['type' => 'bar', 'height' => 280, 'toolbar' => ['show' => false]],
            'series' => [
                ['name' => 'Downloads', 'data' => $downloads],
            ],
            'plotOptions' => ['bar' => ['horizontal' => false, 'columnWidth' => '60%']],
            'xaxis' => ['categories' => $categories, 'labels' => ['rotate' => -30]],
            'yaxis' => [['title' => ['text' => 'Downloads']]],
            'colors' => ['#2196F3'],
            'dataLabels' => ['enabled' => true],
            'tooltip' => ['shared' => true, 'intersect' => false],
        ];
    }

    protected function extraJsOptions(): ?\Filament\Support\RawJs
    {
        return \Filament\Support\RawJs::make(<<<'JS'
        {
            dataLabels: {
                formatter: function (val) { return Intl.NumberFormat().format(val); }
            },
            tooltip: {
                y: {
                    formatter: function (val) { return Intl.NumberFormat().format(val); }
                }
            }
        }
        JS);
    }
}
