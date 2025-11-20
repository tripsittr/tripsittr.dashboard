<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Lightweight wrapper around the Facebook Graph API for Instagram Business features.
 *
 * Notes:
 * - Requires the social account to have `facebook_page_id` and/or `instagram_business_account_id` set.
 * - The access token must have the proper scopes (pages_read_engagement, instagram_basic,
 *   instagram_manage_insights, instagram_content_publish, pages_manage_posts).
 */
class InstagramService
{
    protected SocialAccount $account;
    protected string $baseUrl;
    protected string $token;
    protected string $apiVersion;

    public function __construct(SocialAccount $account)
    {
        $this->account = $account;
        $this->token = (string) $account->access_token;
        $this->apiVersion = config('services.facebook.graph_version', env('GRAPH_API_VERSION', 'v17.0'));
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}";
    }

    protected function request(string $method, string $endpoint, array $query = [], array $body = [])
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $options = [];
        $query['access_token'] = $this->token;

        if (Str::upper($method) === 'GET') {
            $response = Http::acceptJson()->get($url, $query);
        } else {
            $response = Http::acceptJson()->post($url, array_merge($query, $body));
        }

        if ($response->failed()) {
            throw new \RuntimeException('Instagram API request failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Resolve the Instagram Business Account ID for this social account.
     */
    public function instagramAccountId(): ?string
    {
        if (!empty($this->account->instagram_business_account_id)) {
            return $this->account->instagram_business_account_id;
        }

        // If we have a page id, query for its instagram_business_account
        if (!empty($this->account->facebook_page_id)) {
            $resp = $this->request('GET', $this->account->facebook_page_id, ['fields' => 'instagram_business_account{id,username}']);
            return data_get($resp, 'instagram_business_account.id');
        }

        return null;
    }

    /**
     * Get recent media for the Instagram Business Account.
     * Returns list of media objects with common fields.
     */
    public function getMedia(int $limit = 25): array
    {
        $igId = $this->instagramAccountId();
        if (!$igId) {
            return [];
        }

        $fields = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,like_count,comments_count';
        $resp = $this->request('GET', "{$igId}/media", ['fields' => $fields, 'limit' => $limit]);

        return $resp['data'] ?? [];
    }

    /**
     * Get insights for a single media item.
     * Example metrics: "engagement,impressions,reach,profile_views" (depends on the object type).
     */
    public function getMediaInsights(string $mediaId, array $metrics = ['engagement', 'impressions']): array
    {
        $metricStr = implode(',', $metrics);
        $resp = $this->request('GET', "{$mediaId}/insights", ['metric' => $metricStr]);
        return $resp['data'] ?? [];
    }

    /**
     * Publish a photo to Instagram (via the Content Publishing API).
     * Steps: POST /{ig-user-id}/media -> returns creation_id, then POST /{ig-user-id}/media_publish with creation_id
     * Returns publish result.
     */
    public function publishPhoto(string $imageUrl, ?string $caption = null): array
    {
        $igId = $this->instagramAccountId();
        if (!$igId) {
            throw new \RuntimeException('Instagram Business Account ID not available for this social account.');
        }

        // create container
        $create = $this->request('POST', "{$igId}/media", ['image_url' => $imageUrl, 'caption' => $caption]);
        $creationId = $create['id'] ?? null;
        if (!$creationId) {
            throw new \RuntimeException('Failed to create Instagram media container.');
        }

        // publish
        $publish = $this->request('POST', "{$igId}/media_publish", ['creation_id' => $creationId]);
        return $publish;
    }

    /**
     * Convenience: get account-level insights (followers, impressions) if available.
     */
    public function getAccountInsights(array $metrics = ['follower_count']): array
    {
        $igId = $this->instagramAccountId();
        if (!$igId) {
            return [];
        }

        // account insights endpoint differs; use /{ig-user-id}/insights with metrics
        $resp = $this->request('GET', "{$igId}/insights", ['metric' => implode(',', $metrics)]);
        return $resp['data'] ?? [];
    }
}
