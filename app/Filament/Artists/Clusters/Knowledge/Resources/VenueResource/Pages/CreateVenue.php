<?php

namespace App\Filament\Artists\Clusters\Knowledge\Resources\VenueResource\Pages;

use App\Filament\Artists\Clusters\Knowledge\Resources\VenueResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVenue extends CreateRecord {
    protected static string $resource = VenueResource::class;
}
