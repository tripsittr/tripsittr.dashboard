<?php

namespace App\Filament\Artists\Clusters\Music\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\SongAnalytics;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;

class MonthlyMetricsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'songMonthlyMetricsChart';

    protected static ?string $heading = 'Monthly Metrics (last 30 days)';

    protected static ?int $contentHeight = 360;

    protected int|string|array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

    // Allow overriding the date range via query parameters (start_date, end_date)
    $requestStart = request()->query('start_date');
    $requestEnd = request()->query('end_date');

    $end = $requestEnd ? Carbon::parse($requestEnd)->endOfDay() : Carbon::now()->endOfDay();
    $start = $requestStart ? Carbon::parse($requestStart)->startOfDay() : Carbon::now()->subDays(29)->startOfDay(); // 30 days inclusive

        $query = SongAnalytics::selectRaw(
            "DATE(imported_at) as day, SUM(streams) as streams_sum, AVG(streams_pct) as streams_pct_avg, AVG(streams_change_pct) as streams_change_pct_avg"
        )
            ->whereNotNull('imported_at')
            ->whereBetween('imported_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->groupBy(DB::raw('DATE(imported_at)'))
            ->orderBy('day')
            ->get();

        // Build a complete list of days (in case some days have no imports)
        $period = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $period->push($d->format('Y-m-d'));
        }

        $rowsByDay = $query->keyBy('day');

        $categories = $period->map(fn($d) => Carbon::parse($d)->format('M j'))->toArray();

        $streams_series = $period->map(fn($d) => (int) ($rowsByDay[$d]->streams_sum ?? 0))->toArray();
        $streams_pct_series = $period->map(fn($d) => round((float) ($rowsByDay[$d]->streams_pct_avg ?? 0.0), 4))->toArray();
        $streams_change_pct_series = $period->map(fn($d) => round((float) ($rowsByDay[$d]->streams_change_pct_avg ?? 0.0), 4))->toArray();

        return [
            'chart' => ['type' => 'area', 'height' => 360, 'toolbar' => ['show' => false]],
            'series' => [
                ['name' => 'Streams (sum)', 'type' => 'area', 'data' => $streams_series],
            ],
            'stroke' => ['width' => [2], 'curve' => 'smooth'],
            'xaxis' => ['categories' => $categories, 'type' => 'category'],
            'yaxis' => [
                ['title' => ['text' => 'Streams (count)']],
            ],
            'colors' => ['#C75D5D'],
            'fill' => ['type' => ['gradient'], 'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.25, 'opacityTo' => 0.05]],
            'tooltip' => ['shared' => true, 'intersect' => false],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        // Provide a percent formatter for the secondary axis and tooltip
        return RawJs::make(<<<'JS'
        {
            yaxis: [
                {
                    // Primary axis: integer formatting with thousands separators
                    labels: { formatter: function (val) { return Intl.NumberFormat().format(Math.round(val)); } },
                }
            ],
            tooltip: {
                y: {
                    formatter: function (val, opts) {
                        // Streams count — format as integer with thousands separators
                        return Intl.NumberFormat().format(Math.round(val));
                    }
                }
            }
        }
        JS);
    }
}
