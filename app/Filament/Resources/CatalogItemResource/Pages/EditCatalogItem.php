<?php

namespace App\Filament\Resources\CatalogItemResource\Pages;

use App\Filament\Resources\CatalogItemResource;
use Filament\Resources\Pages\EditRecord;

class EditCatalogItem extends EditRecord
{
    protected static string $resource = CatalogItemResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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
