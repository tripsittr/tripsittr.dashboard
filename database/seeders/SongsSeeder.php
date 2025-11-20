<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SongsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('songs')) {
            return;
        }

        $now = now();

        $songs = [
            ['title' => 'Northern Lights', 'album' => 'North Star', 'track_number' => 1, 'duration' => 215],
            ['title' => 'Falling Slow', 'album' => 'North Star', 'track_number' => 2, 'duration' => 189],
            ['title' => 'Echoes', 'album' => 'Midnight Radio', 'track_number' => 1, 'duration' => 242],
            ['title' => 'Harbor View', 'album' => 'Coastal Dreams', 'track_number' => 3, 'duration' => 201],
        ];

        foreach ($songs as $s) {
            $row = [
                'title' => $s['title'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('songs', 'track_number')) {
                $row['track_number'] = $s['track_number'];
            }

            if (Schema::hasColumn('songs', 'duration')) {
                $row['duration'] = $s['duration'];
            }

            if (Schema::hasColumn('songs', 'album')) {
                $row['album'] = $s['album'];
            }

            if (Schema::hasColumn('songs', 'slug')) {
                $row['slug'] = Str::slug($s['title']);
            }

            // Some schemas use polymorphic ownership columns (ownable_type/ownable_id).
            if (Schema::hasColumn('songs', 'ownable_type')) {
                $row['ownable_type'] = 'App\\Models\\Team';
            }

            if (Schema::hasColumn('songs', 'ownable_id')) {
                $row['ownable_id'] = DB::table('teams')->value('id') ?? DB::table('users')->value('id') ?? null;
            }

            DB::table('songs')->updateOrInsert(['title' => $s['title']], $row);
        }
    }
}
