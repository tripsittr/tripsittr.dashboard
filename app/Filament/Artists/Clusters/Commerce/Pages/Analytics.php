<?php

namespace App\Filament\Artists\Clusters\Commerce\Pages;

use App\Filament\Artists\Clusters\Commerce\Commerce as CommerceCluster;
use Filament\Facades\Filament;
use Filament\Pages\Page;

class Analytics extends Page
{
    protected static ?string $cluster = CommerceCluster::class;

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $navigationIcon = 'heroicon-s-chart-bar-square';

    // Filament expects a static $view on BasePage — point to the package page component view.
    protected static string $view = 'filament.clusters.commerce.pages.analytics';

    // Use widgets for the header stats and a footer table for orders.
    public function getHeaderWidgetsColumns(): int|array
    {
        // Use a 2-column header grid so widgets are larger and more readable.
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Artists\Clusters\Commerce\Widgets\CommerceStats::class,
            // Top row: Orders (left) and New Customers (right)
            \App\Filament\Artists\Clusters\Commerce\Widgets\OrdersPerMonthChart::class,
            \App\Filament\Artists\Clusters\Commerce\Widgets\NewCustomersPerMonthChart::class,
            // Full-width revenue chart sits below the two small charts
            \App\Filament\Artists\Clusters\Commerce\Widgets\RevenuePerMonthChart::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        // Reference the widget class directly. The widget sets its own
        // $tableRecordsPerPage property to avoid creating dynamic public
        // properties via `make()` which can cause Livewire serialization issues.
        return [
            \App\Filament\Artists\Clusters\Commerce\Widgets\OrdersTableWidget::class,
        ];
    }
}
