<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TeamsTableSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('teams')) {
            return;
        }

        $now = now();

        $teams = [
            ['name' => 'Arianna Vale Music', 'personal_team' => 0],
            ['name' => 'Hale Studios', 'personal_team' => 0],
            ['name' => 'Delgado Productions', 'personal_team' => 0],
        ];

        foreach ($teams as $t) {
            $row = [
                'name' => $t['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('teams', 'personal_team')) {
                $row['personal_team'] = $t['personal_team'];
            }

            // Some installations may require a `type` column (e.g. polymorphic team types). Provide a sensible default.
            if (Schema::hasColumn('teams', 'type')) {
                $row['type'] = $t['type'] ?? 'artist';
            }

            DB::table('teams')->updateOrInsert(['name' => $t['name']], $row);
        }
    }
}
