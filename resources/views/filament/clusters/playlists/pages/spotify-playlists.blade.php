{{-- filepath: /Users/tripsittr/Documents/GitHub/tripsittr.dashboard/resources/views/filament/clusters/playlists/pages/spotify-playlists.blade.php --}}
<x-filament::page>
    <h1 class="text-2xl font-bold mb-4">Spotify Playlists</h1>

    @if (session('error'))
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if (count($playlists) > 0)
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            @foreach ($playlists as $playlist)
                <div style="border: 1px solid #ddd; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    {{-- Display playlist cover photo --}}
                    @if (isset($playlist['images'][0]['url']))
                        <img src="{{ $playlist['images'][0]['url'] }}" alt="{{ $playlist['name'] }} Cover" style="width: 100%; height: auto; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1rem;">
                    @else
                        <div style="width: 100%; height: 10rem; background-color: #f3f3f3; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="color: #999;">No Image</span>
                        </div>
                    @endif
        
                    {{-- Playlist details --}}
                    <div>
                        <h2 style="font-size: 1.125rem; font-weight: 600;">{{ $playlist['name'] }}</h2>
                        <p style="font-size: 0.875rem; color: #666;">Tracks: {{ $playlist['tracks']['total'] }}</p>
                        </br>
                        <x-filament::button
                            href="{{ $playlist['external_urls']['spotify'] }}"
                            tag="a"
                        >
                            View on Spotify
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No playlists found for this user.</p>
    @endif
</x-filament::page>