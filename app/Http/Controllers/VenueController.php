<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VenueController extends Controller {
    public function share(Request $request, Venue $venue) {
        $request->validate([
            'email' => 'required|email',
            'info' => 'required|array',
        ]);

        $info = $request->input('info');
        $email = $request->input('email');

        $data = [];
        $fields = [
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
            'backline_available',
            'lighting_equipment_provided',
            'green_room',
            'wifi_available',
            'wheelchair_accessible',
            'food_beverage_available',
            'public_transit_access',
            'nearby_hotels',
            'notes',
            'facebook_profile',
            'instagram_handle',
            'linkedin',
            'twitter',
            'whatsapp',
            'youtube',
            'tiktok',
            'has_backstage',
            'climate_control',
            'bag_policy',
            'restroom_info',
            'ticket_types',
            'ticket_policy',
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
            'info_url'
        ];

        foreach ($fields as $field) {
            if (in_array($field, $info)) {
                $data[$field] = $venue->$field;
            }
        }

        Mail::send('emails.share-venue', ['data' => $data], function ($message) use ($email) {
            $message->to($email)
                ->subject('Venue Information');
        });

        return redirect()->back()->with('success', 'Venue information sent successfully!');
    }
}
