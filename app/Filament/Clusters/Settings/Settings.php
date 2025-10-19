<?php

namespace App\Filament\Clusters\Settings;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?int $navigationSort = 100; // place after commerce

    public static function shouldRegisterNavigation(): bool
    {
        // We suppress direct cluster navigation; access via user menu entry.
        return false;
    }
}
