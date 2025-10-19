<x-filament::page>
    @php($auth = \Illuminate\Support\Facades\Auth::user())
    <div class="flex items-start justify-between gap-4 mb-4">
        <h1 class="text-lg font-semibold tracking-tight">Application Settings</h1>
        @php($isAdmin = $this->isAdminFlag ?? false)
        @if($isAdmin)
        <span
            class="inline-flex items-center gap-1 rounded-md bg-rose-500/10 px-2 py-1 text-[11px] font-medium text-rose-600 dark:text-rose-400">
            ADMIN ACCESS
        </span>
        @endif
    </div>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}
        @if($isAdmin)
        <x-filament::button type="submit" color="primary">Save Settings</x-filament::button>
        @endif
    </form>
</x-filament::page>