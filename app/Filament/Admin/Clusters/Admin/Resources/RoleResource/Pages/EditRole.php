<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // permission fields are not DB columns, strip all perm_* and legacy permission_ids
        foreach (array_keys($data) as $key) {
            if ($key === 'permission_ids' || str_starts_with($key, 'perm_')) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        // merge all perm_* fields
        $permIds = collect($state)
            ->filter(fn ($v, $k) => str_starts_with($k, 'perm_'))
            ->flatMap(fn ($ids) => (array) $ids)
            ->unique()
            ->values()
            ->all();
        $permNames = \Spatie\Permission\Models\Permission::query()->whereIn('id', $permIds)->pluck('name')->all();
        $this->record->syncPermissions($permNames);
    }
}
