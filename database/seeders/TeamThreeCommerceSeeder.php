<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TeamThreeCommerceSeeder extends Seeder
{
    public function run()
    {
        // Ensure we run in a transaction for safety
        DB::transaction(function () {
            $teamId = 3;

            // Create some inventory items if they don't exist
            $existing = DB::table('inventory_items')->where('team_id', $teamId)->exists();
            if (! $existing) {
                $items = [
                    ['name' => 'T-Shirt', 'sku' => 'TS-001', 'cost' => 8.50, 'price' => 20.00, 'team_id' => $teamId, 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Sticker Pack', 'sku' => 'ST-001', 'cost' => 0.50, 'price' => 3.00, 'team_id' => $teamId, 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Hoodie', 'sku' => 'HD-001', 'cost' => 18.00, 'price' => 45.00, 'team_id' => $teamId, 'created_at' => now(), 'updated_at' => now()],
                ];
                DB::table('inventory_items')->insert($items);
            }

            $inventory = DB::table('inventory_items')->where('team_id', $teamId)->get();
            if ($inventory->isEmpty()) {
                return;
            }

            // Seed orders across the last 12 months
            $now = Carbon::now();
            $ordersToCreate = [];
            $orderItemsToCreate = [];

            // Generate a varying number of orders per month (1..8)
            for ($m = 11; $m >= 0; $m--) {
                $month = $now->copy()->subMonths($m);
                $ordersCount = rand(2, 8);
                for ($i = 0; $i < $ordersCount; $i++) {
                    $orderDate = $month->copy()->addDays(rand(0, 26))->addHours(rand(0,23))->addMinutes(rand(0,59));
                    $customerId = null;
                    // create order row and collect id after insert
                    $ordersToCreate[] = [
                        'team_id' => $teamId,
                        'customer_id' => $customerId,
                        'status' => 'completed',
                        'total' => 0, // placeholder, updated later
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ];
                }
            }

            // Insert orders and capture inserted ids
            $nowTs = now();
            foreach (array_chunk($ordersToCreate, 100) as $chunk) {
                DB::table('orders')->insert($chunk);
            }

            $allOrders = DB::table('orders')->where('team_id', $teamId)->where('created_at', '>=', $now->copy()->subMonths(12)->startOfMonth())->get();

            foreach ($allOrders as $order) {
                // Each order gets 1-3 items
                $itemCount = rand(1, 3);
                $total = 0;
                for ($k = 0; $k < $itemCount; $k++) {
                    $prod = $inventory->random();
                    $qty = rand(1, 3);
                    $lineTotal = ($prod->price ?? 10.0) * $qty;
                    $cost = ($prod->cost ?? 1.0) * $qty;

                    $orderItemsToCreate[] = [
                        'order_id' => $order->id,
                        'inventory_item_id' => $prod->id,
                        'quantity' => $qty,
                        'line_total' => round($lineTotal, 2),
                        'created_at' => $order->created_at,
                        'updated_at' => $order->created_at,
                    ];

                    $total += $lineTotal;
                }

                // Update order total
                DB::table('orders')->where('id', $order->id)->update(['total' => round($total, 2)]);
            }

            // Insert order items in chunks
            foreach (array_chunk($orderItemsToCreate, 200) as $chunk) {
                DB::table('order_items')->insert($chunk);
            }
        });
    }
}
