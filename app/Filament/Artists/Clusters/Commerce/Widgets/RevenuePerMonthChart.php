<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RevenuePerMonthChart extends ApexChartWidget
{
    protected static ?string $chartId = 'revenuePerMonthChart';

    protected static ?string $heading = 'Revenue vs Cost (12 months)';

    protected static ?int $contentHeight = 300;

    // Allow this chart to sit beside OrdersPerMonthChart in the 2-column grid
    // Make revenue chart full-width beneath the smaller header charts
    protected int|string|array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? 3;

        // Compute earnings and expense. Use LEFT JOIN to include orders without order_items
        // and COALESCE(line_total, orders.total) so we still show earnings when
        // order_items are absent or incomplete.
        $rows = DB::table('orders')
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('inventory_items', 'order_items.inventory_item_id', '=', 'inventory_items.id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month, SUM(COALESCE(order_items.line_total, orders.total)) as earnings, SUM(COALESCE(order_items.quantity * COALESCE(inventory_items.cost, 0), 0)) as expense")
            ->when($teamId, fn ($q) => $q->where('orders.team_id', $teamId))
            ->whereBetween('orders.created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        // If order_items aren't present or the result is empty, fall back to summing orders.total
        $hasData = false;
        foreach ($rows as $r) {
            if (! empty($r->earnings) || ! empty($r->expense)) {
                $hasData = true;
                break;
            }
        }

        // If there's no earnings data (unlikely with the COALESCE above), leave rows as-is.

        // Aggregate earnings and expense per month (last 12 months)
        $rows = DB::table('orders')
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('inventory_items', 'order_items.inventory_item_id', '=', 'inventory_items.id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as ym, SUM(COALESCE(order_items.line_total, orders.total)) as earnings, SUM(COALESCE(order_items.quantity * COALESCE(inventory_items.cost, 0), 0)) as expense")
            ->when($teamId, fn ($q) => $q->where('orders.team_id', $teamId))
            ->whereBetween('orders.created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $earnings = [];
        $expenses = [];
        for ($i = 11; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $ym = $dt->format('Y-m');
            $labels[] = $dt->format('M');
            $row = $rows[$ym] ?? null;
            $earn = $row ? (float) $row->earnings : 0.0;
            $exp = $row ? (float) $row->expense : 0.0;
            // expenses should be negative for stacked visualization
            $earnings[] = $earn;
            $expenses[] = $exp ? -1 * $exp : 0.0;
        }

        $allValues = array_filter(array_merge($earnings, $expenses), fn ($v) => is_numeric($v));
        $minVal = count($allValues) ? floor(min($allValues) * 1.1) : -200;
        $maxVal = count($allValues) ? ceil(max($allValues) * 1.1) : 300;

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 260,
                'parentHeightOffset' => 2,
                'stacked' => true,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                ['name' => 'Earning', 'data' => $earnings],
                ['name' => 'Expense', 'data' => $expenses],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '50%',
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'legend' => [
                'show' => true,
                'horizontalAlign' => 'right',
                'position' => 'top',
                'fontFamily' => 'inherit',
                'markers' => [
                    'height' => 12,
                    'width' => 12,
                    'radius' => 12,
                    'offsetX' => -3,
                    'offsetY' => 2,
                ],
                'itemMargin' => ['horizontal' => 5],
            ],
            'grid' => ['show' => false],
            'xaxis' => [
                'categories' => $labels,
                'labels' => ['style' => ['fontFamily' => 'inherit']],
                'axisTicks' => ['show' => false],
                'axisBorder' => ['show' => false],
            ],
            'yaxis' => [
                'offsetX' => -16,
                'labels' => ['style' => ['fontFamily' => 'inherit']],
                'min' => $minVal,
                'max' => $maxVal,
                'tickAmount' => 5,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#A84A4A', '#7F3434'],
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 1,
                'lineCap' => 'round',
            ],
            // Primary color from panel settings
            'colors' => ['#C75D5D', '#8C3B3B'],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    formatter: function (val, timestamp, opts) {
                        return val
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return '$' + val
                    }
                }
            },
            tooltip: {
                x: {
                    formatter: function (val) {
                        return val + ' /23'
                    }
                }
            }
        }
    JS);
    }
}
