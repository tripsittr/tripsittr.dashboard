<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $now = now();

        $users = [
            ['name' => 'Arianna Vale', 'email' => 'arianna@example.com'],
            ['name' => 'Marcus Hale', 'email' => 'marcus.hale@example.com'],
            ['name' => 'Sofia Delgado', 'email' => 'sofia.delgado@example.com'],
            ['name' => 'Riley Chen', 'email' => 'riley.chen@example.com'],
            ['name' => 'Tomi Adeyemi', 'email' => 'tomi.adeyemi@example.com'],
        ];

        foreach ($users as $u) {
            $row = [
                'name' => $u['name'],
                'email' => $u['email'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('users', 'password')) {
                $row['password'] = Hash::make('password');
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $row['email_verified_at'] = $now;
            }

            // Insert if not exists
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                $row
            );
        }
    }
}
