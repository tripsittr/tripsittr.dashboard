<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class InstagramSocialPage extends Page {
    protected static ?string $navigationGroup = 'Social Media';

    protected static ?string $navigationLabel = 'Instagram';

    protected static string $view = 'filament.pages.instagram-social-page';

    public function mount() {
        $accessToken = config('services.instagram.access_token'); // Ensure this is set in your config/services.php
        $response = Http::get("https://graph.instagram.com/me/media", [
            'fields' => 'id,caption,media_url,media_type',
            'access_token' => $accessToken,
        ]);

        $this->viewData['posts'] = $response->json()['data'] ?? [];
    }
}
