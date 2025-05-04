<?php

namespace App\Filament\Clusters\Knowledge\Resources\VenueResource\Pages;

use App\Filament\Clusters\Knowledge\Resources\VenueResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class ViewVenue extends ViewRecord {
    use HasRecentHistoryRecorder;
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\EditAction::make(),
        ];
    }
}
