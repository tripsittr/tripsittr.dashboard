<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;

class TeamsPurgeDuplicateMembers extends Command
{
    protected $signature = 'teams:purge-duplicate-members {teamId? : Optional team id to limit the purge}';
    protected $description = 'Remove accidental duplicate user pivot rows in team_user table to correct seat counts';

    public function handle(): int
    {
        $query = Team::query();
        if ($id = $this->argument('teamId')) {
            $query->whereKey($id);
        }
        $totalRemoved = 0;
        $query->chunkById(100, function($teams) use (&$totalRemoved) {
            foreach ($teams as $team) {
                $removed = $team->purgeDuplicateMembers();
                if ($removed > 0) {
                    $this->info("Team #{$team->id} removed {$removed} duplicate member row(s).");
                    $totalRemoved += $removed;
                }
            }
        });
        $this->info("Done. Total duplicate rows removed: {$totalRemoved}");
        return self::SUCCESS;
    }
}
