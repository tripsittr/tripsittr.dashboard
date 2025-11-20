<?php

// TripLinkBuilderController: permanently removed implementation.
// The file remains as a harmless stub for now to avoid surprises from any lingering references.

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class TripLinkBuilderController extends Controller
{
    /**
     * Builder endpoints have been permanently removed. If code still calls this controller,
     * return a 410 Gone to signal the resource is no longer available.
     */
    public function __invoke()
    {
        return response('Site Builder removed', 410);
    }
}

