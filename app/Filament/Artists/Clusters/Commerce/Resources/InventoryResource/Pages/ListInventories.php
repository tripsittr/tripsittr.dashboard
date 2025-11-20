<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources\InventoryResource\Pages;

use App\Filament\Artists\Clusters\Commerce\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventories extends ListRecords {
    protected static string $resource = InventoryResource::class;

    protected ?string $heading = 'Inventory';

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
