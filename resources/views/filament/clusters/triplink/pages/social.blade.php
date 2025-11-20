<x-filament-panels::page>
    <x-filament::card>
        <h3 class="text-lg font-medium">Social Media</h3>
        <p class="text-sm text-gray-500">Provide social links as JSON array:
            [{"provider":"twitter","url":"https://...","label":"Twitter"}, ...]</p>
        <form wire:submit.prevent="submit" class="space-y-6">
            <p class="text-sm text-gray-500">Manage your social links and platforms.</p>
            {{ $this->form }}
            <x-filament::button type="submit" color="primary">Save Social</x-filament::button>
        </form>
    </x-filament::card>
</x-filament-panels::page>