<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'catalog_item_id')) {
                $table->unsignedBigInteger('catalog_item_id')->nullable()->after('id');
                $table->index('catalog_item_id');
            }
            if (!Schema::hasColumn('inventory_items', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('inventory_items', 'location')) {
                $table->string('location')->nullable()->after('stock');
            }
            if (!Schema::hasColumn('inventory_items', 'status')) {
                $table->string('status')->default('in_stock')->after('location');
            }
            if (!Schema::hasColumn('inventory_items', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'catalog_item_id')) {
                $table->dropColumn('catalog_item_id');
            }
            if (Schema::hasColumn('inventory_items', 'serial_number')) {
                $table->dropColumn('serial_number');
            }
            if (Schema::hasColumn('inventory_items', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('inventory_items', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('inventory_items', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
