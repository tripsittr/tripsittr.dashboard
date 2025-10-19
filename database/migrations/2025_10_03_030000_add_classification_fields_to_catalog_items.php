<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            if(!Schema::hasColumn('catalog_items','item_type')) $table->string('item_type')->nullable()->index();
            if(!Schema::hasColumn('catalog_items','sizes')) $table->json('sizes')->nullable();
            if(!Schema::hasColumn('catalog_items','colors')) $table->json('colors')->nullable();
            if(!Schema::hasColumn('catalog_items','format')) $table->string('format')->nullable();
            if(!Schema::hasColumn('catalog_items','runtime_minutes')) $table->integer('runtime_minutes')->nullable();
            if(!Schema::hasColumn('catalog_items','warranty_months')) $table->integer('warranty_months')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            foreach(['item_type','sizes','colors','format','runtime_minutes','warranty_months'] as $col){
                if(Schema::hasColumn('catalog_items',$col)) $table->dropColumn($col);
            }
        });
    }
};
