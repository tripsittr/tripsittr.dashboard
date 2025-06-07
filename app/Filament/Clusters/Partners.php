<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Partners extends Cluster
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
