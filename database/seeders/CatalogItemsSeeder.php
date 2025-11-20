<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CatalogItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('catalog_items')) {
            return;
        }

        $now = now();

        $items = [
            ['title' => 'North Star (Vinyl)', 'sku' => 'NS-VIN-001', 'price' => 24.99, 'stock' => 12],
            ['title' => 'Midnight Radio (Deluxe)', 'sku' => 'MR-DLX-001', 'price' => 14.99, 'stock' => 30],
            ['title' => 'Coastal Dreams Hooded', 'sku' => 'CD-HOD-XL', 'price' => 39.0, 'stock' => 7],
        ];

        foreach ($items as $i) {
            $row = [
                'title' => $i['title'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('catalog_items', 'sku')) {
                $row['sku'] = $i['sku'];
            }

            if (Schema::hasColumn('catalog_items', 'price')) {
                $row['price'] = $i['price'];
            }

            if (Schema::hasColumn('catalog_items', 'stock')) {
                $row['stock'] = $i['stock'];
            }

            // Determine a usable unique key for updateOrInsert. If the table doesn't have 'sku' or 'title', skip.
            if (Schema::hasColumn('catalog_items', 'sku')) {
                $uniqueKey = ['sku' => $i['sku']];
            } elseif (Schema::hasColumn('catalog_items', 'title')) {
                $uniqueKey = ['title' => $i['title']];
            } else {
                // No suitable unique column to match on — skip this item.
                continue;
            }

            DB::table('catalog_items')->updateOrInsert($uniqueKey, $row);
        }
    }
}
