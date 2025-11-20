<?php

namespace App\Models;

use App\Filament\Index\Traits\BlacklistedWordsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use BlacklistedWordsTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address_1',
        'address_2',
        'country',
        'city',
        'state',
        'zip',
        'lat',
        'lng',
        'phone',
        'email',
        'url',
        'catagories',
        'capacity',
        'indoor_outdoor',
        'stage_size',
        'seating_type',
        'parking_info',
        'age_restriction',
        'alcohol_policy',
        'booking_contact_name',
        'booking_email',
        'booking_phone',
        'booking_website',
        'rental_price_range',
        'sound_equipment_provided',
        'lighting_equipment_provided',
        'backline_available',
        'green_room',
        'wifi_available',
        'wheelchair_accessible',
        'food_beverage_available',
        'nearby_hotels',
        'public_transit_access',
        'notes',
        'facebook_profile',
        'instagram_handle',
        'linkedin',
        'twitter',
        'whatsapp',
        'youtube',
        'tiktok',
        'bag_policy',
        'ticket_policy',
        'ticket_types',
        'bo_address_1',
        'bo_address_2',
        'bo_city',
        'bo_state',
        'bo_zip',
        'bo_country',
        'bo_phone',
        'bo_email',
        'bo_url',
        'bo_hours',
        'bo_notes',
        'info_url',
        'parking_info',
        'climate_control',
    ];

    protected $casts = [
        'catagories' => 'array',
        'parking_info' => 'array',
        'stage_size' => 'string',
        'seating_type' => 'array',
        'indoor_outdoor' => 'string',
        'climate_control' => 'array',
        'age_restriction' => 'string',
        'alcohol_policy' => 'string',
        'ticket_policy' => 'array',
        'bag_policy' => 'array',
        'ticket_types' => 'array',
        'capacity' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'sound_equipment_provided' => 'boolean',
        'lighting_equipment_provided' => 'boolean',
        'backline_available' => 'boolean',
        'green_room' => 'boolean',
        'wifi_available' => 'boolean',
        'wheelchair_accessible' => 'boolean',
        'food_beverage_available' => 'boolean',
        'public_transit_access' => 'boolean',
    ];

    public function getBlacklistedFields(): array
    {
        return array_merge($this->fillable, ['name', 'description']);
    }
}
