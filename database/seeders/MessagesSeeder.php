<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MessagesSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('direct_message_threads') && ! Schema::hasTable('messages')) {
            return;
        }

        $now = now();

        // If there's a threads table, seed a thread and messages; otherwise seed messages table directly.
        if (Schema::hasTable('direct_message_threads')) {
            $threadId = DB::table('direct_message_threads')->updateOrInsert([
                'subject' => 'Intro to the team'
            ], [
                'subject' => 'Intro to the team',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // insert sample message if messages table exists
            if (Schema::hasTable('messages')) {
                DB::table('messages')->updateOrInsert([
                    'thread_id' => $threadId,
                    'body' => 'Welcome to the group — let us know if you need anything.'
                ], [
                    'thread_id' => $threadId,
                    'body' => 'Welcome to the group — let us know if you need anything.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return;
        }

        // fallback: insert a single message row
        if (Schema::hasTable('messages')) {
            DB::table('messages')->updateOrInsert([
                'body' => 'Welcome to the platform! This is a seeded message.'
            ], [
                'body' => 'Welcome to the platform! This is a seeded message.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
