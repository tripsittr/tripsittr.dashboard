<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $items = $data['items'] ?? [];
        $subtotal = collect($items)->sum(fn($i) => (float) ($i['line_total'] ?? 0));
        $data['subtotal'] = $subtotal;
        $data['tax_total'] = 0;
        $data['total'] = $subtotal + ($data['shipping_cost'] ?? 0);
        return $data;
    }
}
