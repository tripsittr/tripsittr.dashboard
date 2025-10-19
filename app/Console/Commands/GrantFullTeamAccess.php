<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Team;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class GrantFullTeamAccess extends Command
{
    protected $signature = 'permissions:grant-full {userId=5} {--team=* : Specific team IDs (repeatable); if omitted applies to all user teams}';
    protected $description = 'Grant full (Admin) role in the specified user\'s teams (team-scoped roles).';

    public function handle(): int
    {
        $userId = (int) $this->argument('userId');
        $user = User::find($userId);
        if(! $user) {
            $this->error("User {$userId} not found");
            return self::FAILURE;
        }

        $teamIds = collect($this->option('team'))->filter()->map(fn($id)=>(int)$id);
        $teams = $teamIds->isNotEmpty() ? Team::whereIn('id',$teamIds)->get() : $user->teams;
        if($teams->isEmpty()){
            $this->warn('No teams resolved for this user.');
            return self::SUCCESS;
        }

        $granted = 0;
        foreach($teams as $team){
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
            $role = Role::firstOrCreate([
                'name' => 'Admin',
                'team_id' => $team->id,
                'guard_name' => 'web',
            ]);
            if(! $user->roles()->where('roles.id',$role->id)->exists()){
                $user->assignRole($role->name);
                $granted++;
                $this->info("Granted Admin in team {$team->id} ({$team->name})");
            } else {
                $this->line("Already Admin in team {$team->id} ({$team->name})");
            }
        }

        $this->info("Done. {$granted} new Admin assignments.");
        return self::SUCCESS;
    }
}
