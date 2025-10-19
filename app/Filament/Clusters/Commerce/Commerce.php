<?php

namespace App\Filament\Clusters\Commerce;

use Filament\Clusters\Cluster;

class Commerce extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-s-building-storefront';
    protected static ?string $navigationLabel = 'Commerce';
    protected static ?int $navigationSort = 10;
}
