<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('url')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->unsignedTinyInteger('star_count')->nullable();
            $table->unsignedInteger('rating_count')->nullable();
            $table->string('zip')->nullable();
            $table->string('primary_category_name');
            $table->text('category_name')->nullable();

            // Capacity & Event Details
            $table->unsignedInteger('capacity')->nullable();
            $table->enum('indoor_outdoor', ['indoor', 'outdoor', 'both'])->nullable();
            $table->string('stage_size')->nullable();
            $table->string('seating_type')->nullable();
            $table->text('parking_info')->nullable();
            $table->string('age_restriction')->nullable();
            $table->string('alcohol_policy')->nullable();

            // Booking & Contact Info
            $table->string('booking_contact_name')->nullable();
            $table->string('booking_email')->nullable();
            $table->string('booking_phone')->nullable();
            $table->string('booking_website')->nullable();
            $table->string('rental_price_range')->nullable();

            // Technical Specs & Equipment
            $table->boolean('sound_equipment_provided')->default(false);
            $table->boolean('lighting_equipment_provided')->default(false);
            $table->boolean('backline_available')->default(false);
            $table->boolean('green_room')->default(false);
            $table->boolean('wifi_available')->default(false);

            // Accessibility & Additional Services
            $table->boolean('wheelchair_accessible')->default(false);
            $table->boolean('food_beverage_available')->default(false);
            $table->text('nearby_hotels')->nullable();
            $table->boolean('public_transit_access')->default(false);
            $table->text('notes')->nullable();

            // Social Media
            $table->string('facebook_profile')->nullable();
            $table->string('instagram_handle')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('venues');
    }
};



// name, address, phone, email, lat, lng, url, country, state, city, star_count, rating_count, zip, 
// primary_category_name, category_name, capacity, indoor_outdoor, stage_size, seating_type, parking_info, 
// age_restriction, alcohol_policy, booking_contact_name, booking_email, booking_phone, booking_website, 
// rental_price_range, sound_equipment_provided, lighting_equipment_provided, backline_available, green_room, 
// wifi_available, wheelchair_accessible, food_beverage_available, nearby_hotels, public_transit_access, notes, 
// facebook_profile, instagram_handle, linkedin, twitter, whatsapp, youtube, tiktok
