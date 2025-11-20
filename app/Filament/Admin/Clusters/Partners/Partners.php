<?php
namespace App\Filament\Admin\Clusters\Partners;

use Filament\Clusters\Cluster;

class Partners extends Cluster
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
