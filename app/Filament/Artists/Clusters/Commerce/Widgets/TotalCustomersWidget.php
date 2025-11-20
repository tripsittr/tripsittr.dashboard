<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use App\Models\Customer;

class TotalCustomersWidget extends BaseWidget
{
    // Make this a single-column stat so it pairs nicely with the Orders chart
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $teamId = $tenant?->id;

        $customersQuery = Customer::query();
        if ($teamId) {
            $customersQuery->where('team_id', $teamId);
        }

        return [
            Stat::make('Total Customers', $customersQuery->count()),
        ];
    }
}
