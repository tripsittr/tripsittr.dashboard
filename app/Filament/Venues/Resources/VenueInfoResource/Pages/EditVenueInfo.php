<?php

namespace App\Filament\Venues\Resources\VenueInfoResource\Pages;

use App\Filament\Venues\Resources\VenueInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueInfo extends EditRecord
{
    protected static string $resource = VenueInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
