<?php

namespace App\Filament\Venues\Resources\VenueEventResource\Pages;

use App\Filament\Venues\Resources\VenueEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVenueEvent extends ViewRecord
{
    protected static string $resource = VenueEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
