<x-filament::page>
    <div class="flex items-start justify-between gap-4 mb-4">
        <h1 class="text-lg font-semibold tracking-tight">TripLink — Links</h1>
    </div>

    <form wire:submit.prevent="submit" class="space-y-6">
        <p class="text-sm text-gray-500">Manage your links. Use the Add Link button to add more entries.</p>
        {{ $this->form }}
        <x-filament::button type="submit" color="primary">Save Links</x-filament::button>
    </form>
</x-filament::page>