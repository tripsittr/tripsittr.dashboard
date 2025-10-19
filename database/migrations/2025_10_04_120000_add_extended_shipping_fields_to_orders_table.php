<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function(Blueprint $table){
            if(!Schema::hasColumn('orders','shipping_first_name')){
                $table->string('shipping_first_name')->nullable()->after('status');
            }
            if(!Schema::hasColumn('orders','shipping_last_name')){
                $table->string('shipping_last_name')->nullable()->after('shipping_first_name');
            }
            if(!Schema::hasColumn('orders','shipping_company')){
                $table->string('shipping_company')->nullable()->after('shipping_last_name');
            }
            if(!Schema::hasColumn('orders','shipping_reference')){
                $table->string('shipping_reference')->nullable()->after('shipping_company');
            }
            if(!Schema::hasColumn('orders','shipping_reference_2')){
                $table->string('shipping_reference_2')->nullable()->after('shipping_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function(Blueprint $table){
            $table->dropColumn([
                'shipping_first_name','shipping_last_name','shipping_company','shipping_reference','shipping_reference_2'
            ]);
        });
    }
};
