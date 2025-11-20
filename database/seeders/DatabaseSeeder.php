<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Keep ActionSeeder as-is; run other seeders to populate realistic data.
        $this->call([
            ActionSeeder::class,
            UsersTableSeeder::class,
            TeamsTableSeeder::class,
            AlbumsSeeder::class,
            SongsSeeder::class,
            CustomersSeeder::class,
            CatalogItemsSeeder::class,
            OrdersSeeder::class,
            VenuesSeeder::class,
            EventsSeeder::class,
            InventorySeeder::class,
            KnowledgeSeeder::class,
            MessagesSeeder::class,
        ]);
    }
}
