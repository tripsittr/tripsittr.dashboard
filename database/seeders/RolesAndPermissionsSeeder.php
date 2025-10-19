<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Team;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            'catalog items','customers','orders','inventory items','activity logs'
        ];
        $actions = ['view','view_any','create','update','delete'];
        foreach($entities as $entity){
            foreach($actions as $action){
                Permission::firstOrCreate(['name' => $action.' '. $entity]);
            }
        }

        $teams = Team::all();
        if ($teams->isEmpty()) {
            $this->command?->warn('No teams found: skipping team-scoped role creation.');
        }
        foreach ($teams as $team) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);

            $admin = Role::firstOrCreate([
                'name' => 'Admin',
                'team_id' => $team->id,
                'guard_name' => 'web',
            ]);
            $manager = Role::firstOrCreate([
                'name' => 'Manager',
                'team_id' => $team->id,
                'guard_name' => 'web',
            ]);
            $member = Role::firstOrCreate([
                'name' => 'Member',
                'team_id' => $team->id,
                'guard_name' => 'web',
            ]);

            $admin->syncPermissions(Permission::all());
            $manager->syncPermissions(Permission::whereIn('name', [
                'view catalog items','view_any catalog items','create catalog items','update catalog items',
                'view customers','view_any customers','create customers','update customers',
                'view orders','view_any orders','create orders','update orders',
                'view inventory items','view_any inventory items','create inventory items','update inventory items',
                'view activity logs','view_any activity logs'
            ])->get());

            $member->syncPermissions(Permission::whereIn('name', [
                'view catalog items','view_any catalog items',
                'view customers','view_any customers',
                'view orders','view_any orders',
                'view inventory items','view_any inventory items'
            ])->get());
        }
    }
}
