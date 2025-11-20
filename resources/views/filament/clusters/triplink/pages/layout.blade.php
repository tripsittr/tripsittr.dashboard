<div>
    <x-filament::page>
        <div class="flex items-start justify-between gap-4 mb-4">
            <h1 class="text-lg font-semibold tracking-tight">TripLink — Layout</h1>
            @php $slug = $data['slug'] ?? null; @endphp
            @if ($slug)
            <a href="{{ url('/u/' . $slug) }}" target="_blank"
                class="filament-button inline-flex items-center gap-2 px-3 py-1 rounded text-sm bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-100">Preview</a>
            @else
            <button disabled
                class="filament-button inline-flex items-center gap-2 px-3 py-1 rounded text-sm opacity-50 bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-100">Preview</button>
            @endif
        </div>

        <form wire:submit.prevent="submit" class="space-y-6">
            <p class="text-sm text-gray-500">Layout blocks configuration.</p>
            <div class="grid items-start gap-3" style="grid-template-columns: 1fr;">
                <div>
                    {{ $this->form }}
                    <x-filament::button type="submit" color="primary">Save Layout</x-filament::button>
                </div>
            </div>
        </form>

        {{-- Live preview removed --}}
    </x-filament::page>
</div>