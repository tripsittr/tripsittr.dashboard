<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('team_id');
            $table->string('part_number'); // master part number
            $table->string('reference_code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('material')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('default_cost', 10, 2)->nullable();
            $table->decimal('default_price', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->enum('dims_unit', ['cm','in','mm','ft','m'])->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->enum('weight_unit', ['kg','lbs'])->nullable();
            $table->unsignedInteger('default_lead_time_days')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['team_id', 'part_number']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
