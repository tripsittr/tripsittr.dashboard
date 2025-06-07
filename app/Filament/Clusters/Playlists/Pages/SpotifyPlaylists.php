<?php

namespace App\Filament\Clusters\Playlists\Pages;

use App\Filament\Clusters\Playlists;
use App\Http\Integrations\SpotifyConnector\SpotifyConnector;
use App\Http\Integrations\SpotifyUserPlaylistsRequest\Requests\SpotifyUserPlaylistsRequest;
use Filament\Pages\Page;

class SpotifyPlaylists extends Page
{

    protected static string $view = 'filament.clusters.playlists.pages.spotify-playlists';
    protected static ?string $cluster = Playlists::class;
    protected static ?string $navigationLabel = 'Spotify Playlists';
    protected static ?string $slug = 'spotify-playlists';

    public $playlists = [];

    public function mount(): void
    {
        try {
            $connector = new SpotifyConnector();

            $response = $connector->send(new class extends \Saloon\Http\Request {
                protected \Saloon\Enums\Method $method = \Saloon\Enums\Method::GET;

                public function resolveEndpoint(): string
                {
                    return '/me/playlists';
                }
            });

            $this->playlists = $response->json('items');
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to fetch playlists: ' . $e->getMessage());
        }
    }

    protected function getViewData(): array
    {
        return [
            'playlists' => $this->playlists,
        ];
    }
}
