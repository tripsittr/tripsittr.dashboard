<?php

namespace App\Filament\Artists\Clusters\Music\Pages;

use App\Filament\Artists\Clusters\Music\Music as MusicCluster;
use Filament\Pages\Page;

class SongAnalytics extends Page
{
    protected static ?string $cluster = MusicCluster::class;

    protected static ?string $navigationLabel = 'Song Analytics';

    protected static ?string $navigationIcon = 'heroicon-s-chart-bar';

    protected static string $view = 'filament.clusters.music.pages.song-analytics';

    public function getHeaderWidgetsColumns(): int|array
    {
        // Use a 2-column header grid like the Commerce analytics page for better readability.
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Make the streams count chart full-width and keep other metric widgets.
            \App\Filament\Artists\Clusters\Music\Widgets\StreamsChart::class,
            \App\Filament\Artists\Clusters\Music\Widgets\MonthlyMetricsChart::class,
            \App\Filament\Artists\Clusters\Music\Widgets\DownloadsChart::class,
            \App\Filament\Artists\Clusters\Music\Widgets\TopMovers::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Artists\Clusters\Music\Widgets\SongAnalyticsTableWidget::class,
        ];
    }
}
