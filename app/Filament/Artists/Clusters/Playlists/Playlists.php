<?php

namespace App\Filament\Artists\Clusters\Playlists;

use Filament\Clusters\Cluster;

class Playlists extends Cluster
{
    protected static ?string $navigationGroup = 'Extras';

    protected static bool $shouldRegisterNavigation = true;
}
