<?php

namespace App\Filament\Artists\Clusters\Knowledge;

use Filament\Clusters\Cluster;

class Knowledge extends Cluster {
    protected static ?string $navigationIcon = 'fas-book';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
