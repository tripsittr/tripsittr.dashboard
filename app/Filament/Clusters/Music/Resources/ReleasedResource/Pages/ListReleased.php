<?php
namespace App\Filament\Clusters\Music\Resources\ReleasedResource\Pages;

use App\Filament\Clusters\Music\Resources\ReleasedResource;
use Filament\Resources\Pages\ListRecords;

class ListReleased extends ListRecords
{
    protected static string $resource = ReleasedResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
