<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;

class FacebookTokenService
{
    protected string $apiVersion;

    public function __construct()
    {
        $this->apiVersion = config('services.facebook.graph_version', env('GRAPH_API_VERSION', 'v17.0'));
    }

    /**
     * Exchange a short-lived token for a long-lived token using the Graph API.
     * Returns the JSON response (access_token, token_type, expires_in) on success.
     */
    public function exchangeForLongLivedToken(SocialAccount $account): array
    {
        $appId = config('services.facebook.client_id');
        $appSecret = config('services.facebook.client_secret');

        $resp = Http::acceptJson()->get("https://graph.facebook.com/{$this->apiVersion}/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => (string) $account->access_token,
        ]);

        if ($resp->failed()) {
            throw new \RuntimeException('Facebook token exchange failed: ' . $resp->body());
        }

        return $resp->json();
    }

    /**
     * Refresh a long-lived token by re-exchanging the current token. Returns the response.
     */
    public function refreshToken(SocialAccount $account): array
    {
        // FB allows re-exchanging the token to extend expiration; reuse the same endpoint
        return $this->exchangeForLongLivedToken($account);
    }
}
