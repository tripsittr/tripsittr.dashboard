<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('knowledge') && ! Schema::hasTable('knowledges') && ! Schema::hasTable('knowledge_base')) {
            return;
        }

        $table = Schema::hasTable('knowledge') ? 'knowledge' : (Schema::hasTable('knowledges') ? 'knowledges' : 'knowledge_base');

        $now = now();

        $items = [
            ['title' => 'How to submit a track', 'slug' => 'submit-track', 'content' => 'Guidelines for submitting a track to the platform.'],
            ['title' => 'Managing your merchandise', 'slug' => 'manage-merch', 'content' => 'Tips for inventory and merchandising.'],
        ];

        foreach ($items as $it) {
            $row = [
                'title' => $it['title'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn($table, 'slug')) {
                $row['slug'] = $it['slug'];
            }

            if (Schema::hasColumn($table, 'content')) {
                $row['content'] = $it['content'];
            }

            DB::table($table)->updateOrInsert(['title' => $it['title']], $row);
        }
    }
}
