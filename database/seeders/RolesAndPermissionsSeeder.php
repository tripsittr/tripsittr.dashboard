<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder {
    public function run() {
        // General Permissions
        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'manage permissions',
            'manage teams',
            'view teams',
            'edit teams',
            'delete teams',
            'manage merchandise',
            'manage orders',
            'manage analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Team-Specific Permissions
        foreach (range(1, 10) as $teamId) { // Adjust range for team count
            Permission::firstOrCreate(['name' => "manage team-$teamId"]);
            Permission::firstOrCreate(['name' => "invite members-team-$teamId"]);
            Permission::firstOrCreate(['name' => "edit team settings-team-$teamId"]);
        }

        // Band-Specific Permissions
        foreach (range(1, 10) as $bandId) { // Adjust range for bands
            Permission::firstOrCreate(['name' => "manage band-$bandId"]);
            Permission::firstOrCreate(['name' => "invite members-band-$bandId"]);
            Permission::firstOrCreate(['name' => "edit band settings-band-$bandId"]);
            Permission::firstOrCreate(['name' => "add songs-band-$bandId"]);
            Permission::firstOrCreate(['name' => "add albums-band-$bandId"]);
            Permission::firstOrCreate(['name' => "manage merch-band-$bandId"]);
            Permission::firstOrCreate(['name' => "view band-$bandId"]);
        }

        // Roles & Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        $teamOwnerRole = Role::firstOrCreate(['name' => 'Team Owner']);
        $teamManagerRole = Role::firstOrCreate(['name' => 'Team Manager']);
        $teamMemberRole = Role::firstOrCreate(['name' => 'Team Member']);

        $bandOwnerRole = Role::firstOrCreate(['name' => 'Band Owner']);
        $bandManagerRole = Role::firstOrCreate(['name' => 'Band Manager']);
        $bandMemberRole = Role::firstOrCreate(['name' => 'Band Member']);

        // Assign General Permissions
        $adminRole->syncPermissions($permissions);
        $managerRole->syncPermissions(['view dashboard', 'manage users', 'manage teams']);
        $userRole->syncPermissions(['view dashboard', 'view teams']);

        // Assign Team-Specific Permissions
        $teamOwnerRole->syncPermissions([
            "manage team-1",
            "invite members-team-1",
            "edit team settings-team-1",
        ]);
        $teamManagerRole->syncPermissions([
            "manage team-1",
            "invite members-team-1",
        ]);
        $teamMemberRole->syncPermissions(["view teams"]);

        // Assign Band-Specific Permissions
        $bandOwnerRole->syncPermissions([
            "manage band-1",
            "invite members-band-1",
            "edit band settings-band-1",
            "add songs-band-1",
            "add albums-band-1",
            "manage merch-band-1",
            "view band-1",
        ]);
        $bandManagerRole->syncPermissions([
            "edit band settings-band-1",
            "add songs-band-1",
            "add albums-band-1",
            "manage merch-band-1",
            "view band-1",
        ]);
        $bandMemberRole->syncPermissions(["view band-1"]);
    }
}
