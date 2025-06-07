<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class Admin extends Cluster
{
    protected static ?string $navigationIcon = 'fas-lock';

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return Filament::getTenant()->type === 'admin' || Filament::getTenant()->type === 'Admin';
    }
}
