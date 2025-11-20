<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\PartnersResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\PartnersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
