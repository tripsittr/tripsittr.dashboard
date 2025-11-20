<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_items') && ! Schema::hasColumn('inventory_items', 'catalog_item_id')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->foreignId('catalog_item_id')->nullable()->after('id')->constrained('catalog_items')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_items') && Schema::hasColumn('inventory_items', 'catalog_item_id')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->dropForeign(['catalog_item_id']);
                $table->dropColumn('catalog_item_id');
            });
        }
    }
};
