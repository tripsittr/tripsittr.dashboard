<x-filament::page>
    <div class="space-y-6">
        <div class="prose dark:prose-invert">
            <h2>Verify Track Details</h2>
            <p>Please review the required information before submitting this track for review.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
            $map = [
            'file_present' => 'Audio file present',
            'duration_set' => 'Duration detected',
            'artwork_present' => 'Artwork uploaded',
            'title_present' => 'Title set',
            'genre_present' => 'Genre selected',
            'primary_artists_present' => 'Primary artist(s) set',
            'release_date_set' => 'Release date set',
            'isrc_present' => 'ISRC present (recommended)',
            ];
            @endphp
            @foreach($this->checks as $key => $ok)
            <div class="flex items-center justify-between rounded-md border p-3"
                :class="{ 'border-green-300 bg-green-50 dark:bg-green-900/20': {{ $ok ? 'true' : 'false' }}, 'border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20': {{ !$ok ? 'true' : 'false' }} }">
                <div class="text-sm font-medium">{{ $map[$key] ?? Str::headline($key) }}</div>
                <x-filament::badge :color="$ok ? 'success' : 'warning'">{{ $ok ? 'OK' : 'Missing' }}</x-filament::badge>
            </div>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <x-filament::button color="primary" wire:click="submit">Submit for review</x-filament::button>
            <x-filament::button color="gray" tag="a"
                :href="\App\Filament\Clusters\Music\Resources\TracksResource::getUrl('edit', ['record' => $this->record])">
                Edit track
            </x-filament::button>
        </div>
    </div>
</x-filament::page>