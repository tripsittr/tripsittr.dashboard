<?php

namespace App\Http\Integrations\SpotifyUserPlaylistsRequest\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SpotifyUserPlaylistsRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/users/{$this->userId}/playlists';
    }
}
