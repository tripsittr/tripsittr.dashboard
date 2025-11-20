<?php

namespace App\Filament\Venues\Resources\VenueEventResource\Pages;

use App\Filament\Venues\Resources\VenueEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueEvent extends EditRecord
{
    protected static string $resource = VenueEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
