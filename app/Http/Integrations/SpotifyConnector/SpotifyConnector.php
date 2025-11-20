<?php

namespace App\Http\Integrations\SpotifyConnector;

use App\Filament\Index\Services\SpotifyService;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class SpotifyConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base URL for the Spotify API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.spotify.com/v1';
    }

    /**
     * Default headers for the Spotify API.
     */
    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.(new SpotifyService)->getSpotifyAccessToken(),
        ];
    }
}
