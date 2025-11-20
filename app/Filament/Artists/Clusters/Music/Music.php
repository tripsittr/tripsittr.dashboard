<?php

namespace App\Filament\Artists\Clusters\Music;

use Filament\Clusters\Cluster;

class Music extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-musical-note';
    protected static ?string $navigationLabel = 'Music';
    protected static ?int $navigationSort = 20; // position among clusters

    public static function shouldRegisterNavigation(): bool
    {
        return true; // Show cluster in nav
    }
}
