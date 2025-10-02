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
            $table->boolean('sound_equipment_provided')->nullable()->default(false);
            $table->boolean('backline_available')->nullable()->default(false);
            $table->boolean('lighting_equipment_provided')->nullable()->default(false);
            $table->boolean('green_room')->nullable()->default(false);
            $table->boolean('wifi_available')->nullable()->default(false);
            $table->boolean('wheelchair_accessible')->nullable()->default(false);
            $table->boolean('food_beverage_available')->nullable()->default(false);
            $table->boolean('public_transit_access')->nullable()->default(false);
            $table->text('nearby_hotels')->nullable();
            $table->text('notes')->nullable();
            $table->string('facebook_profile')->nullable();
            $table->string('instagram_handle')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('has_backstage')->nullable();
            $table->text('climate_control')->nullable();
            $table->text('bag_policy')->nullable();
            $table->text('restroom_info')->nullable();
            $table->text('ticket_types')->nullable();
            $table->text('ticket_policy')->nullable();
            $table->string('bo_address_1', 45)->nullable();
            $table->string('bo_address_2', 45)->nullable();
            $table->string('bo_city', 45)->nullable();
            $table->string('bo_state', 45)->nullable();
            $table->string('bo_zip', 45)->nullable();
            $table->string('bo_country', 45)->nullable();
            $table->string('bo_phone', 45)->nullable();
            $table->string('bo_email', 45)->nullable();
            $table->string('bo_url', 45)->nullable();
            $table->string('bo_hours', 90)->nullable();
            $table->text('bo_notes')->nullable();
            $table->string('info_url', 45)->nullable();
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
