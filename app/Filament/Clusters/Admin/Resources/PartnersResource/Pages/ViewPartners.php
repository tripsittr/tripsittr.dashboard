<?php

namespace App\Filament\Clusters\Admin\Resources\PartnersResource\Pages;

use App\Filament\Clusters\Admin\Resources\PartnersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPartners extends ViewRecord
{
    protected static string $resource = PartnersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
