<?php

namespace App\Filament\Venues\Resources\VenueInfoResource\Pages;

use App\Filament\Venues\Resources\VenueInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueInfos extends ListRecords
{
    protected static string $resource = VenueInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
