<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources\CatalogItemResource\Pages;

use App\Filament\Artists\Clusters\Commerce\Resources\CatalogItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCatalogItem extends CreateRecord
{
    protected static string $resource = CatalogItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['item_type'] ?? null) !== 'clothing') {
            $data['sizes'] = null;
            $data['colors'] = null;
        }
        if (! in_array($data['item_type'] ?? null, ['generic','equipment'])) {
            $data['length'] = $data['width'] = $data['height'] = $data['dims_unit'] = null;
            $data['weight'] = $data['weight_unit'] = null;
        }
        if (($data['item_type'] ?? null) !== 'media') {
            $data['format'] = null; $data['runtime_minutes'] = null;
        }
        if (($data['item_type'] ?? null) !== 'equipment') {
            $data['warranty_months'] = null;
        }
        return $data;
    }
}
