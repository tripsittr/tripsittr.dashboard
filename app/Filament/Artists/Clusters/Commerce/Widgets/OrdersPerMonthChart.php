<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class OrdersPerMonthChart extends ApexChartWidget
{
    protected static ?string $chartId = 'ordersPerMonthChart';

    protected static ?string $heading = 'Orders per Month (12 months)';

    protected static ?int $contentHeight = 260;

    // Span a single column in the 2-column header grid
    protected int|string|array $columnSpan = 1;

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        $rows = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('team_id', $teamId)
            ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $labels = [];
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $labels[] = $m;
            $data[] = isset($rows[$m]) ? (int) $rows[$m] : 0;
        }

        $primary = '#C75D5D';

        return [
            'chart' => ['type' => 'bar', 'height' => 260, 'toolbar' => ['show' => false]],
            'series' => [['name' => 'Orders', 'data' => $data]],
            'xaxis' => ['categories' => $labels, 'labels' => ['rotate' => -30]],
            'colors' => [$primary],
            'stroke' => ['curve' => 'smooth', 'width' => 2],
            'markers' => ['size' => 4],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        // Avoid returning RawJs here; return null to prevent Livewire from
        // trying to serialize JS functions.
        return null;
    }
}
