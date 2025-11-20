<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripLink;

class TripLinkController extends Controller
{
    // Public profile
    public function show($slug)
    {
        $trip = TripLink::where('slug', $slug)->where('published', true)->firstOrFail();

        // Always render the standard TripLink public view
        return view('triplinks.show', ['trip' => $trip]);
    }

    // Admin updates for different sections (basic auth middleware expected on routes)
    // Admin updates are handled in Filament pages; keep public show endpoint only.
}
