<?php
namespace App\Filament\Index\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class SpotifyService
{
    function getSpotifyAccessToken(): string
    {
        $filePath = storage_path('spotify_refresh_token.txt');

        if (!file_exists($filePath)) {
            throw new Exception('Refresh token file not found. Please log in to Spotify again.');
        }

        $refreshToken = file_get_contents($filePath);

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        $data = $response->json();

        // Log the response for debugging
        logger('Spotify Token Response:', $data);

        if (isset($data['access_token'])) {
            return $data['access_token'];
        }

        // Throw an exception if the access token is not returned
        throw new Exception('Unable to fetch Spotify access token. Response: ' . json_encode($data));
    }
}
