<?php

namespace App\Filament\Artists\Clusters\SocialMedia;

use Filament\Clusters\Cluster;

class SocialMedia extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-share';
    protected static ?string $navigationLabel = 'Social Media';
    protected static ?int $navigationSort = 35;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
