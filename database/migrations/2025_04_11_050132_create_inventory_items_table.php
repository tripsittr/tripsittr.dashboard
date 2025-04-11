<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku');
            $table->string('batch_number')->nullable();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price')->nullable();
            $table->decimal('cost')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->decimal('length')->nullable();
            $table->decimal('width')->nullable();
            $table->decimal('height')->nullable();
            $table->enum('dims_unit', ['cm', 'in', 'mm', 'ft', 'm'])->nullable();
            $table->decimal('weight')->nullable();
            $table->enum('weight_unit', ['kg', 'lbs'])->nullable();
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'])->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('team_id');

            $table->primary(['id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
