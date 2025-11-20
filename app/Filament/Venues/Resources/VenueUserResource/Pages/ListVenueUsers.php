<?php

namespace App\Filament\Venues\Resources\VenueUserResource\Pages;

use App\Filament\Venues\Resources\VenueUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueUsers extends ListRecords
{
    protected static string $resource = VenueUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
