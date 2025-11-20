<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TripLinkPreviewController
{
    public function preview(Request $request)
    {
        // Accept layout state from editor and render a preview HTML fragment.
        $state = $request->input('state', []);

        // Basic normalization
    $layout = is_array($state['layout'] ?? null) ? $state['layout'] : [];
    $title = $state['title'] ?? ($state['data']['title'] ?? '');
    $bio = $state['bio'] ?? ($state['data']['bio'] ?? '');
    $design = $state['design'] ?? ($state['data']['design'] ?? []);
    // Support uploaded fonts in the editor state (array of storage paths)
    $fonts = $state['fonts'] ?? ($state['data']['fonts'] ?? []);

        // Render the preview using a Blade view partial and return the HTML
    $html = view('triplinks._preview', compact('layout', 'title', 'bio', 'design', 'fonts'))->render();

        return response()->json(['html' => $html]);
    }
}
