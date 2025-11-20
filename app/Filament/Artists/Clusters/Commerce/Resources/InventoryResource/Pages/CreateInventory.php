<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources\InventoryResource\Pages;

use App\Filament\Artists\Clusters\Commerce\Resources\InventoryResource;
use App\Models\InventoryItem;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\ButtonAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;

class CreateInventory extends CreateRecord {
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\Action::make('generateSku')
                ->label('Generate SKU')
                ->icon('heroicon-s-cog')
                ->modalHeading('Generate SKU')
                ->form([
                    TextInput::make('sku')
                        ->label('Generated SKU')
                        ->default(fn() => $this->generateSku())
                        ->disabled(),
                ])
                ->modalSubmitAction(false)
                ->modalCancelAction(fn(StaticAction $action) => $action->label('Close')),
            Actions\Action::make('generateBatch')
                ->label('Generate Batch')
                ->icon('heroicon-s-cog')
                ->modalHeading('Generate Batch')
                ->form([
                    TextInput::make('batch')
                        ->label('Generated Batch')
                        ->default(fn() => $this->generateBatch())
                        ->disabled(),
                ])
                ->modalSubmitAction(false)
                ->modalCancelAction(fn(StaticAction $action) => $action->label('Close')),
        ];
    }

    public function generateSku(): string {
        $currentYear = date('Y');
        $nextId = (DB::table('inventory_items')->max('id') ?? 0) + 1;
        $paddedId = str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return $currentYear . $paddedId;
    }

    public function generateBatch(): string {
        return 'INV' . random_int(1000, 9999);
    }

    public function generateBarcode(): string {
        if (!$this->record) {
            return 'N/A';
        }

        $sku = $this->record->sku ?? 'UNKNOWN';
        $batch = $this->record->batch ?? 'UNKNOWN';
        $createdAt = $this->record->created_at ? $this->record->created_at->format('Ymd') : '00000000';

        return $sku . '-' . $batch . '-' . $createdAt;
    }
}
