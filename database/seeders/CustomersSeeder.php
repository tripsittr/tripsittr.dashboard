<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        $now = now();

        $customers = [
            ['name' => 'Hannah Pierce', 'email' => 'hannah.pierce@musicfans.com'],
            ['name' => 'Ethan Park', 'email' => 'ethan.park@example.net'],
            ['name' => 'Maya Ortiz', 'email' => 'maya.ortiz@fanmail.org'],
        ];

        foreach ($customers as $c) {
            $row = [
                'name' => $c['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('customers', 'email')) {
                $row['email'] = $c['email'];
            }

            DB::table('customers')->updateOrInsert(['email' => $c['email']], $row);
        }
    }
}
