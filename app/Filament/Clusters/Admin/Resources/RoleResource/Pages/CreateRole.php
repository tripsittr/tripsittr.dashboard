<?php

namespace App\Filament\Clusters\Admin\Resources\RoleResource\Pages;

use App\Filament\Clusters\Admin\Resources\RoleResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = Filament::getTenant();
        $data['guard_name'] = 'web';
        $data['team_id'] = $tenant->id ?? null;
        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
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
