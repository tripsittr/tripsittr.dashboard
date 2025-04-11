<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class InstagramSocialPage extends Page {
    protected static ?string $navigationGroup = 'Social Media';

    protected static ?string $navigationLabel = 'Instagram';

    protected static string $view = 'filament.pages.instagram-social-page';

    function fetchInstagramPosts($user) {
        $integration = $user->integrations()->where('service', 'facebook')->first();

        if (!$integration) {
            throw new \Exception('Instagram account not linked.');
        }

        $response = Http::withToken($integration->access_token)
            ->get('https://graph.facebook.com/v16.0/me/media', [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,timestamp',
            ]);

        return $response->json();
    }
}
