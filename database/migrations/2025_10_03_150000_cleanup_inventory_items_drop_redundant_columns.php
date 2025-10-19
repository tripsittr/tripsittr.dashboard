<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $drops = [
                'name','description','material','brand','price','cost','length','width','height','dims_unit','weight','weight_unit'
            ];
            foreach ($drops as $col) {
                if (Schema::hasColumn('inventory_items', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (! Schema::hasColumn('inventory_items','override_price')) {
                $table->boolean('override_price')->default(false)->after('status');
            }
            if (! Schema::hasColumn('inventory_items','price_override')) {
                $table->decimal('price_override',10,2)->nullable()->after('override_price');
            }
            if (! Schema::hasColumn('inventory_items','override_cost')) {
                $table->boolean('override_cost')->default(false)->after('price_override');
            }
            if (! Schema::hasColumn('inventory_items','cost_override')) {
                $table->decimal('cost_override',10,2)->nullable()->after('override_cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Recreate dropped columns (minimal definitions)
            if (! Schema::hasColumn('inventory_items','name')) $table->string('name')->nullable();
            if (! Schema::hasColumn('inventory_items','description')) $table->text('description')->nullable();
            if (! Schema::hasColumn('inventory_items','material')) $table->string('material')->nullable();
            if (! Schema::hasColumn('inventory_items','brand')) $table->string('brand')->nullable();
            if (! Schema::hasColumn('inventory_items','price')) $table->decimal('price',10,2)->nullable();
            if (! Schema::hasColumn('inventory_items','cost')) $table->decimal('cost',10,2)->nullable();
            if (! Schema::hasColumn('inventory_items','length')) $table->decimal('length',10,2)->nullable();
            if (! Schema::hasColumn('inventory_items','width')) $table->decimal('width',10,2)->nullable();
            if (! Schema::hasColumn('inventory_items','height')) $table->decimal('height',10,2)->nullable();
            if (! Schema::hasColumn('inventory_items','dims_unit')) $table->string('dims_unit',10)->nullable();
            if (! Schema::hasColumn('inventory_items','weight')) $table->decimal('weight',10,2)->nullable();
            if (! Schema::hasColumn('inventory_items','weight_unit')) $table->string('weight_unit',10)->nullable();

            foreach(['override_price','price_override','override_cost','cost_override'] as $col){
                if (Schema::hasColumn('inventory_items',$col)) $table->dropColumn($col);
            }
        });
    }
};
