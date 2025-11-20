<x-filament::page>
    <div class="flex items-start justify-between gap-4 mb-4">
        <h1 class="text-lg font-semibold tracking-tight">TripLink — Header & Design</h1>
        <?php $slug = $this->form->getRawState()['slug'] ?? null; ?>
        @if($slug)
        <a href="{{ url('/u/' . $slug) }}" target="_blank"
            class="filament-button inline-flex items-center gap-2 px-3 py-1 rounded bg-gray-100 text-sm">Preview</a>
        @else
        <button disabled
            class="filament-button inline-flex items-center gap-2 px-3 py-1 rounded bg-gray-100 text-sm opacity-50">Preview</button>
        @endif
    </div>

    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit" color="primary">Save</x-filament::button>
    </form>
</x-filament::page>