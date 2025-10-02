<x-filament-widgets::widget>
    <x-filament::section collapsible>

        <x-slot name="heading">
            Websites for Artists
        </x-slot>

        <p class="text-sm text-gray-500">Visit the websites below to view analytics for each platform, manage your music
            and profiles, and find additional resources for each once you have released your first Song, Album, or EP.
        </p><br>
        <div class="items-center mb-3">
            <x-filament::button outlined
                href="{{ env('app_url') }}{{ Filament\Facades\Filament::getTenant()->id }}/knowledge/knowledge/artist-platforms-explained"
                tag="a">
                Learn More
            </x-filament::button>
        </div><br>
        <div class="flex flex-row justify-between items-center gap-2">
            <x-filament::icon-button icon="si-spotify" href="https://artists.spotify.com/" tag="a" size="xl"
                tooltip="Spotify for Artists">
            </x-filament::icon-button>
            <x-filament::icon-button icon="si-applemusic" href="https://artists.apple.com/" tag="a" size="xl"
                tooltip="Apple Music for Artists">
            </x-filament::icon-button>
            <x-filament::icon-button icon="si-amazonmusic" href="https://artists.amazon.com/" tag="a" size="xl"
                tooltip="Amazon Music for Artists">
            </x-filament::icon-button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>