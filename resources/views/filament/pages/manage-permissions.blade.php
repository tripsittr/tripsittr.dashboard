<x-filament::page>
    <form wire:submit.prevent="updatePermissions">
        {{ $this->form }}
        <div class="mt-4">
            <x-filament::button type="submit">Save Changes</x-filament::button>
        </div>
    </form>
</x-filament::page>
