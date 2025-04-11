<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('merchandise', function (Blueprint $table) {
            $table->string('sku')->unique()->nullable();
            $table->decimal('weight', 8, 2)->nullable(); // In lbs/kg
            $table->string('dimensions')->nullable(); // e.g., "10x10x2 inches"
            $table->integer('low_stock_threshold')->default(5); // Alert when stock is low
            $table->json('colors')->nullable(); // Store available colors
            $table->string('material')->nullable(); // Material type (Cotton, Metal, etc.)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('merchandise', function (Blueprint $table) {
            //
        });
    }
};
