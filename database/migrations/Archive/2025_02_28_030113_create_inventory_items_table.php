<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('batch_number')->nullable();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->date('exp_date')->nullable();

            // Dimensions stored as separate fields with unit
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->enum('dims_unit', ['cm', 'in', 'mm', 'ft', 'm'])->nullable();

            // Weight stored with unit
            $table->decimal('weight', 8, 2)->nullable();
            $table->enum('weight_unit', ['kg', 'lbs'])->nullable();

            // Standardized size options
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'])->nullable();

            // Additional attributes
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_website')->nullable();

            // Inventory control
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(5);

            // Image storage
            $table->string('image')->nullable();

            // Relationships
            $table->foreignId('band_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('inventory_items');
    }
};
