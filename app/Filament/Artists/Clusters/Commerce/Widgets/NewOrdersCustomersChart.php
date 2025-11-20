<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;

class NewOrdersCustomersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'newOrdersCustomersChart';
    protected static ?string $heading = 'New Orders & Customers (12 months)';
    protected static ?int $contentHeight = 300;

    // This chart is more readable at full width below the smaller stats/charts
    protected int|string|array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        $orders = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('team_id', $teamId)
            ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $customers = DB::table('customers')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('team_id', $teamId)
            ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $labels = [];
        $ordersData = [];
        $customersData = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $labels[] = $m;
            $ordersData[] = isset($orders[$m]) ? (int) $orders[$m] : 0;
            $customersData[] = isset($customers[$m]) ? (int) $customers[$m] : 0;
        }

        $primary = '#C75D5D';
        $muted = '#64748B';

        return [
            'chart' => ['type' => 'line', 'height' => 300, 'toolbar' => ['show' => false]],
            'series' => [
                ['name' => 'Orders', 'data' => $ordersData],
                ['name' => 'New Customers', 'data' => $customersData],
            ],
            'xaxis' => ['categories' => $labels, 'labels' => ['rotate' => -30]],
            'yaxis' => [],
            'colors' => [$primary, $muted],
            'stroke' => ['curve' => 'smooth'],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make("{
            xaxis: { labels: { formatter: function(val){ var parts = val.split('-'); return new Date(parts[0], parts[1]-1).toLocaleString(undefined, { month: 'short' }); } } },
            yaxis: { labels: { formatter: function(val){ return parseInt(val); } } }
        }");
    }
}
