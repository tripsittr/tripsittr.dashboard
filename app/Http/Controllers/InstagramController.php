<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramController extends Controller {
    private $clientId = env('YOUR_CLIENT_ID');
    private $clientSecret = env('YOUR_CLIENT_SECRET');
    private $redirectUri = env('YOUR_REDIRECT_URI');

    public function redirectToInstagram() {
        $authUrl = "https://api.instagram.com/oauth/authorize?client_id={$this->clientId}&redirect_uri={$this->redirectUri}&scope=user_profile,user_media&response_type=code";
        return redirect($authUrl);
    }

    public function handleInstagramCallback(Request $request) {
        $code = $request->input('code');

        $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ]);

        $accessToken = $response->json()['access_token'];
        session(['instagram_access_token' => $accessToken]);

        return redirect()->route('instagram.analytics');
    }

    public function fetchAnalytics() {
        $accessToken = session('instagram_access_token');

        if (!$accessToken) {
            return redirect()->route('instagram.login')->with('error', 'Please log in to Instagram first.');
        }

        // Step 1: Get the connected Facebook Pages
        $pagesResponse = Http::get("https://graph.facebook.com/v16.0/me/accounts", [
            'access_token' => $accessToken,
        ]);

        if ($pagesResponse->failed()) {
            return redirect()->route('instagram.login')->with('error', 'Failed to fetch Facebook Pages.');
        }

        $pages = $pagesResponse->json()['data'];
        $pageId = $pages[0]['id']; // Assuming the first page is linked to the Instagram account

        // Step 2: Get the Instagram Business Account ID
        $igAccountResponse = Http::get("https://graph.facebook.com/v16.0/{$pageId}?fields=instagram_business_account", [
            'access_token' => $accessToken,
        ]);

        if ($igAccountResponse->failed() || !isset($igAccountResponse->json()['instagram_business_account'])) {
            return redirect()->route('instagram.login')->with('error', 'Failed to fetch Instagram Business Account.');
        }

        $igBusinessAccountId = $igAccountResponse->json()['instagram_business_account']['id'];

        // Step 3: Fetch Instagram Insights
        $insightsResponse = Http::get("https://graph.facebook.com/v16.0/{$igBusinessAccountId}/insights", [
            'metric' => 'impressions,reach,profile_views',
            'period' => 'day',
            'access_token' => $accessToken,
        ]);

        if ($insightsResponse->failed()) {
            return redirect()->route('instagram.login')->with('error', 'Failed to fetch Instagram Insights.');
        }

        $insights = $insightsResponse->json()['data'];

        return view('filament.pages.instagram-analytics', ['insights' => $insights]);
    }
}
