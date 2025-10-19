<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\UserType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeamRoleSeeder extends Seeder
{
    public function run(): void
    {
        $types = UserType::query()->pluck('name')->all();
        if (empty($types)) {
            return;
        }

        // Default permission matrix (empty by default). Customize as needed.
        $defaultMatrix = [
            // 'Manager' => ['view artists', 'view songs'],
            // 'Marketing Specialist' => ['manage social posts'],
            // ... add mappings here
        ];

        $allPerms = Permission::query()->pluck('name', 'id');

        foreach (Team::query()->get(['id']) as $team) {
            foreach ($types as $typeName) {
                $role = Role::firstOrCreate([
                    'name' => $typeName,
                    'team_id' => $team->id,
                    'guard_name' => 'web',
                ]);

                // Attach default permissions if provided in matrix
                $permNames = $defaultMatrix[$typeName] ?? [];
                if (! empty($permNames)) {
                    $valid = array_values(array_intersect($allPerms->values()->all(), $permNames));
                    $role->syncPermissions($valid);
                }
            }
        }
    }
}
