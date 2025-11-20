<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlbumsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('albums')) {
            return;
        }

        $now = now();

        $albums = [
            ['title' => 'North Star', 'artist' => 'Arianna Vale', 'release_date' => '2024-05-10'],
            ['title' => 'Midnight Radio', 'artist' => 'Marcus Hale', 'release_date' => '2022-11-03'],
            ['title' => 'Coastal Dreams', 'artist' => 'Sofia Delgado', 'release_date' => '2023-07-21'],
        ];

        foreach ($albums as $a) {
            $row = [
                'title' => $a['title'],
                'created_at' => $now,
                'updated_at' => $now,
            ];


            if (Schema::hasColumn('albums', 'release_date')) {
                $row['release_date'] = $a['release_date'];
            }

            if (Schema::hasColumn('albums', 'artist')) {
                $row['artist'] = $a['artist'];
            }

            // If the albums table has a non-nullable team_id, attach to the first seeded team.
            if (Schema::hasColumn('albums', 'team_id')) {
                $row['team_id'] = DB::table('teams')->value('id') ?? null;
            }

            DB::table('albums')->updateOrInsert(['title' => $a['title']], $row);
        }
    }
}
