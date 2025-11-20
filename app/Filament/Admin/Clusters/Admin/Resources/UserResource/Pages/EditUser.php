<?php
namespace App\Filament\Admin\Clusters\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Clusters\Admin\Resources\UserResource;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    use HasRecentHistoryRecorder;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove permission_ids from persisted attributes; we'll sync after save
        unset($data['permission_ids'], $data['role_ids']);

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $permIds = (array) ($data['permission_ids'] ?? []);
        $roleIds = (array) ($data['role_ids'] ?? []);

        $permNames = Permission::query()->whereIn('id', $permIds)->pluck('name')->all();

        $tenant = Filament::getTenant();
        if ($tenant) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }

        $this->record->syncPermissions($permNames);

        // Sync roles team-scoped
        $roles = \Spatie\Permission\Models\Role::query()
            ->when($tenant, fn ($q) => $q->where('team_id', $tenant->id), fn ($q) => $q->whereNull('team_id'))
            ->whereIn('id', $roleIds)
            ->pluck('name')
            ->all();
        $this->record->syncRoles($roles);

        // Optionally ensure the selected user type exists as a role; do not force exclusive
        $type = trim((string) ($this->record->type ?? ''));
        $teamId = $tenant->id ?? null;
        if ($type !== '') {
            Role::firstOrCreate([
                'name' => $type,
                'team_id' => $teamId,
                'guard_name' => 'web',
            ]);
        }
    }
}
