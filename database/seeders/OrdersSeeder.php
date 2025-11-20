<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        $now = now();

        $orders = [
            ['reference' => 'ORD-1001', 'status' => 'completed', 'total' => 59.98],
            ['reference' => 'ORD-1002', 'status' => 'processing', 'total' => 24.99],
            ['reference' => 'ORD-1003', 'status' => 'cancelled', 'total' => 39.00],
        ];

        foreach ($orders as $o) {
            $row = [
                'reference' => $o['reference'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('orders', 'status')) {
                $row['status'] = $o['status'];
            }

            if (Schema::hasColumn('orders', 'total')) {
                $row['total'] = $o['total'];
            }

            DB::table('orders')->updateOrInsert(['reference' => $o['reference']], $row);
        }
    }
}
