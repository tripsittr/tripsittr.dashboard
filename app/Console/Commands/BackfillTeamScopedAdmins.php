<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class BackfillTeamScopedAdmins extends Command
{
    protected $signature = 'permissions:backfill-team-admins {--dry-run}';
    protected $description = 'Assign Admin role (team-scoped) to first chronological member of each team if not already assigned.';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $count = 0; $skipped = 0;
        $bar = $this->output->createProgressBar(Team::count());
        $bar->start();
        Team::with('users')->orderBy('id')->chunk(50, function($teams) use (&$count,&$skipped,$dry,$bar){
            foreach($teams as $team){
                $bar->advance();
                $firstUser = $team->users()->orderBy('team_user.created_at')->first();
                if(! $firstUser){ $skipped++; continue; }
                app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
                $role = Role::firstOrCreate(['name'=>'Admin','team_id'=>$team->id,'guard_name'=>'web']);
                if(! $firstUser->roles()->where('roles.id',$role->id)->exists()){
                    if(!$dry){ $firstUser->assignRole($role->name); }
                    $count++;
                } else {
                    $skipped++;
                }
            }
        });
        $bar->finish();
        $this->newLine();
        $this->info("Assigned Admin to {$count} first members. Skipped {$skipped} (already admin / no members).");
        return self::SUCCESS;
    }
}
