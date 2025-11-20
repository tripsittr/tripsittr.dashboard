<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use App\Models\Order;
use App\Models\Customer;
use App\Models\InventoryItem;

class CommerceStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $teamId = $tenant?->id;
        $ordersQuery = Order::query();
        $customersQuery = Customer::query();
        $inventoryQuery = InventoryItem::query();

        if ($teamId) {
            $ordersQuery->where('team_id', $teamId);
            $customersQuery->where('team_id', $teamId);
            $inventoryQuery->where('team_id', $teamId);
        }

        // Build 12-month windows for sparklines
        $start = now()->subMonths(11)->startOfMonth();
        $end = now()->endOfMonth();
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        // Orders per month
        $ordersPerMonth = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt")
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('ym')
            ->pluck('cnt', 'ym')
            ->toArray();

        // Sales (sum total) per month
        $salesPerMonth = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(COALESCE(total,0)) as sum")
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('ym')
            ->pluck('sum', 'ym')
            ->toArray();

        // New customers per month
        $customersPerMonth = Customer::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt")
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('ym')
            ->pluck('cnt', 'ym')
            ->toArray();

        // Low stock snapshot (same value for each month - inventory history not tracked)
        $lowStockCount = $inventoryQuery->whereNotNull('stock')->whereColumn('stock', '<=', 'low_stock_threshold')->count();

        $ordersSpark = [];
        $salesSpark = [];
        $customersSpark = [];
        $lowStockSpark = [];
        foreach ($months as $m) {
            $ordersSpark[] = (int) ($ordersPerMonth[$m] ?? 0);
            $salesSpark[] = round((float) ($salesPerMonth[$m] ?? 0), 2);
            $customersSpark[] = (int) ($customersPerMonth[$m] ?? 0);
            $lowStockSpark[] = $lowStockCount;
        }

        return [
            Stat::make('Orders', $ordersQuery->count())
                ->chart($ordersSpark)
                ->chartColor('primary'),

            Stat::make('Sales', '$'.number_format((float) $ordersQuery->whereNotNull('total')->sum('total'), 2))
                ->chart($salesSpark)
                ->chartColor('success'),

            Stat::make('Customers', $customersQuery->count())
                ->chart($customersSpark)
                ->chartColor('secondary'),

            Stat::make('Low stock', $lowStockCount)
                ->chart($lowStockSpark)
                ->chartColor('danger'),
        ];
    }
}
