<?php

namespace App\Filament\Artists\Clusters\TripLink;

use Filament\Clusters\Cluster;

class TripLink extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-link';
    protected static ?string $navigationLabel = 'TripLinks';
    protected static ?int $navigationSort = 30;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
