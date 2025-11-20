<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('inventories') && ! Schema::hasTable('inventory')) {
            return;
        }

        $now = now();

        $items = [
            ['sku' => 'NS-VIN-001', 'name' => 'North Star (Vinyl)', 'quantity' => 12],
            ['sku' => 'CD-HOD-XL', 'name' => 'Coastal Dreams Hooded (XL)', 'quantity' => 7],
        ];

        $table = Schema::hasTable('inventories') ? 'inventories' : 'inventory';

        foreach ($items as $i) {
            $row = [
                'name' => $i['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn($table, 'sku')) {
                $row['sku'] = $i['sku'];
            }

            if (Schema::hasColumn($table, 'quantity')) {
                $row['quantity'] = $i['quantity'];
            }

            if (Schema::hasColumn($table, 'stock')) {
                $row['stock'] = $i['quantity'];
            }

            DB::table($table)->updateOrInsert(['name' => $i['name']], $row);
        }
    }
}
