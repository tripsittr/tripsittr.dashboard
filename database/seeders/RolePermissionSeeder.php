<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define an initial obvious mapping. Teams/tenants are handled per team by team_id.
        $matrix = [
            'Manager' => [
                'view users', 'view roles', 'view permissions',
                'view songs', 'view albums', 'view events', 'manage bookings',
            ],
            'Marketing Specialist' => [
                'manage social posts', 'view social posts',
            ],
            'Sound Engineer' => [
                'view songs', 'edit songs',
            ],
            'Tour Manager' => [
                'view events', 'manage bookings',
            ],
            'Event Organizer' => [
                'view events', 'create events', 'edit events', 'delete events',
            ],
            'Merchandiser' => [
                'manage merch', 'view merch',
            ],
            'Customer Support' => [
                'view users',
            ],
        ];

        // Apply for all roles that exist; permissions must be seeded already.
        foreach (Role::query()->get() as $role) {
            $perms = $matrix[$role->name] ?? [];
            if (! empty($perms)) {
                $role->syncPermissions($perms);
            }
        }
    }
}
