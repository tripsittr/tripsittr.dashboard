<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-4">
            <x-filament::section heading="Recent Activity Logs">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs text-gray-500">Showing latest activity (use full log for advanced filters)</div>
                    <x-filament::button size="xs" color="primary" tag="a"
                        href="{{ \App\Filament\Resources\ActivityLogResource::getUrl() }}">Open Full Log
                    </x-filament::button>
                </div>
                {{ $this->table }}
            </x-filament::section>
        </div>
        <div class="space-y-4">
            <x-filament::section heading="Unread Notifications">
                <ul class="space-y-3">
                    @forelse($this->unreadNotifications as $note)
                    <li class="p-3 rounded border flex justify-between items-start">
                        <div>
                            <div class="text-sm font-medium">{{ $note->data['message'] ?? ($note->data['title'] ??
                                'Notification') }}</div>
                            <div class="text-xs text-gray-500">{{ $note->created_at->diffForHumans() }}</div>
                        </div>
                        <x-filament::button size="xs" color="gray" wire:click="markNotificationRead('{{ $note->id }}')">
                            Mark Read</x-filament::button>
                    </li>
                    @empty
                    <li class="text-sm text-gray-500">No unread notifications.</li>
                    @endforelse
                </ul>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>