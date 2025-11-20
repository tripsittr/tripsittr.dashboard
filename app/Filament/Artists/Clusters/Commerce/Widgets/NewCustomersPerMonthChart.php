<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NewCustomersPerMonthChart extends ApexChartWidget
{
    protected static ?string $chartId = 'newCustomersPerMonthChart';

    protected static ?string $heading = 'New Customers (12 months)';

    protected static ?int $contentHeight = 260;

    // Span a single column in the 2-column header grid
    protected int|string|array $columnSpan = 1;

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        $rows = DB::table('customers')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
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
            'chart' => ['type' => 'line', 'height' => 260, 'toolbar' => ['show' => false]],
            'series' => [['name' => 'New Customers', 'data' => $data]],
            'xaxis' => ['categories' => $labels, 'labels' => ['rotate' => -30]],
            'colors' => [$primary],
            'stroke' => ['curve' => 'smooth'],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        // Removed RawJs to avoid Livewire serialization issues.
        return null;
    }
}
