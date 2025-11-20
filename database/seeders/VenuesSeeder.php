<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VenuesSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('venues')) {
            return;
        }

        $now = now();

        $venues = [
            ['name' => 'The Anchor Hall', 'city' => 'Seattle', 'capacity' => 450],
            ['name' => 'Luna Gardens', 'city' => 'Austin', 'capacity' => 1200],
        ];

        foreach ($venues as $v) {
            $row = [
                'name' => $v['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('venues', 'city')) {
                $row['city'] = $v['city'];
            }

            if (Schema::hasColumn('venues', 'capacity')) {
                $row['capacity'] = $v['capacity'];
            }

            DB::table('venues')->updateOrInsert(['name' => $v['name']], $row);
        }
    }
}
