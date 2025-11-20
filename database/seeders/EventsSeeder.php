<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('venue_events') && ! Schema::hasTable('events')) {
            return;
        }

        $now = now();

        $events = [
            ['title' => 'North Star Listening Party', 'date' => '2025-11-15', 'venue' => 'The Anchor Hall'],
            ['title' => 'Coastal Dreams Tour - Austin', 'date' => '2025-09-30', 'venue' => 'Luna Gardens'],
        ];

        $table = Schema::hasTable('venue_events') ? 'venue_events' : 'events';

        foreach ($events as $e) {
            $row = [
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn($table, 'date')) {
                $row['date'] = $e['date'];
            }

            if (Schema::hasColumn($table, 'venue')) {
                $row['venue'] = $e['venue'];
            }

            // Choose a unique key depending on available columns and set corresponding row values
            if (Schema::hasColumn($table, 'title')) {
                $uniqueKey = ['title' => $e['title']];
                $row['title'] = $e['title'];
            } elseif (Schema::hasColumn($table, 'name')) {
                $uniqueKey = ['name' => $e['title']];
                $row['name'] = $e['title'];
            } elseif (Schema::hasColumn($table, 'slug')) {
                $slug = str($e['title'])->slug();
                $uniqueKey = ['slug' => $slug];
                $row['slug'] = $slug;
            } else {
                // No suitable unique column to match on — skip
                continue;
            }

            DB::table($table)->updateOrInsert($uniqueKey, $row);
        }
    }
}
