<?php

namespace App\Filament\Venues\Resources\VenueEventResource\Pages;

use App\Filament\Venues\Resources\VenueEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueEvents extends ListRecords
{
    protected static string $resource = VenueEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
