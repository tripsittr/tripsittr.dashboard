<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Backfill tenant_id for legacy rows: attempt to infer from related catalog item or leave null.
        // Adjust logic if you maintain a catalog_items.tenant_id field.
        if (Schema::hasTable('inventory_items')) {
            // Example strategy: if catalog_items has tenant_id, use it.
            if (Schema::hasTable('catalog_items') && Schema::hasColumn('catalog_items','tenant_id')) {
                DB::statement('UPDATE inventory_items ii JOIN catalog_items ci ON ci.id = ii.catalog_item_id SET ii.tenant_id = ci.tenant_id WHERE ii.tenant_id IS NULL AND ci.tenant_id IS NOT NULL');
            }
        }
    }

    public function down(): void
    {
        // No-op reversal: we won't null tenant_id again.
    }
};
