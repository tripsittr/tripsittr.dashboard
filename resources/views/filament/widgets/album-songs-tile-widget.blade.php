<div>
    <div class="mb-4 flex items-center gap-4">
        <label for="gridSize" class="font-semibold">Grid Size:</label>
        <select id="gridSize" wire:model.lazy="gridSize"
            class="border rounded px-2 py-1 bg-white dark:bg-gray-800 dark:text-gray-200">
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
    </div>

    @php
    use Filament\Facades\Filament;
    @endphp

    <div class="grid gap-4" style="grid-template-columns: repeat({{ $gridSize }}, minmax(0, 1fr));">
        @foreach ($this->getSongsProperty() as $song)
        <div
            class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-col items-center border border-[0.5px] border-gray-200 dark:border-gray-700">
            <a href="{{ route('filament.admin.resources.songs.view', ['record' => $song->id, 'tenant' => Filament::getTenant()]) }}"
                class="w-full h-full flex flex-col items-center">
                <img src="{{ $song->artwork ?? '/default-artwork.jpg' }}" alt="Artwork"
                    class="w-24 h-24 object-cover rounded mb-2">
                <div class="font-bold text-lg mb-1 dark:text-gray-100">{{ $song->title }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Genre: {{ $song->genre }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status: {{ $song->status }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Release: {{ $song->release_date ?
                    \Carbon\Carbon::parse($song->release_date)->format('M d, Y') : 'N/A' }}</div>
            </a>
        </div>
        @endforeach
    </div>
</div>