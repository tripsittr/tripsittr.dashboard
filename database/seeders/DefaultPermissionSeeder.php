<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DefaultPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            // User & Team
            'manage users',
            'view users',
            'manage roles',
            'view roles',
            'manage permissions',
            'view permissions',

            // Catalog
            'view songs',
            'create songs',
            'edit songs',
            'delete songs',
            'view albums',
            'create albums',
            'edit albums',
            'delete albums',

            // Social / Marketing
            'manage social posts',
            'view social posts',

            // Events / Bookings
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage bookings',

            // Sales / Merch
            'manage merch',
            'view merch',
        ];

        foreach ($perms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
