<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class EditVenue extends EditRecord {
    use HasRecentHistoryRecorder;
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
