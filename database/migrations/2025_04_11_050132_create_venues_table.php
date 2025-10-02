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
        Schema::create('venues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('address_1')->nullable();
            $table->string('address_2', 45)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            $table->json('catagories')->nullable();
            $table->bigInteger('capacity')->nullable();
            $table->enum('indoor_outdoor', ['indoor', 'outdoor', 'both'])->nullable();
            $table->string('stage_size')->nullable();
            $table->string('seating_type')->nullable();
            $table->text('parking_info')->nullable();
            $table->text('age_restriction')->nullable();
            $table->text('alcohol_policy')->nullable();
            $table->string('booking_contact_name')->nullable();
            $table->string('booking_email')->nullable();
            $table->string('booking_phone')->nullable();
            $table->string('booking_website')->nullable();
            $table->string('rental_price_range')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
