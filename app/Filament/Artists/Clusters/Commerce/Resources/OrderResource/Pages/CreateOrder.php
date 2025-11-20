<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources\OrderResource\Pages;

use App\Filament\Artists\Clusters\Commerce\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate totals from repeater items
        $items = $data['items'] ?? [];
        $subtotal = collect($items)->sum(fn($i) => (float) ($i['line_total'] ?? 0));
        $data['subtotal'] = $subtotal;
        $data['tax_total'] = 0; // placeholder
        $data['total'] = $subtotal + ($data['shipping_cost'] ?? 0);
        return $data;
    }
}
