<x-filament-widgets::widget>
    <x-filament::section heading="Recent Activity">
        <form wire:submit.prevent="noop" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="filterAction">
                        <option value="">All Actions</option>
                        @foreach($actionChoices as $key => $label)
                        <option value="{{ $key }}" @selected($filterAction===$key)>{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="dateFrom" />
                </x-filament::input.wrapper>
            </div>
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="dateTo" />
                </x-filament::input.wrapper>
            </div>
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="filterUser">
                        <option value="">All Users</option>
                        @foreach($userChoices as $value => $label)
                        <option value="{{ $value }}" @selected($filterUser==$value)>User #{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="filterTeam">
                        <option value="">All Teams</option>
                        @foreach($teamChoices as $value => $label)
                        <option value="{{ $value }}" @selected($filterTeam==$value)>Team #{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div class="flex items-center gap-2 md:col-span-4">
                <x-filament::button color="secondary"
                    wire:click="$set('filterAction', null); $set('dateFrom', null); $set('dateTo', null)">Reset
                </x-filament::button>
            </div>
        </form>
        <ul class="space-y-3">
            @forelse($items as $item)
            <li class="flex items-start gap-3">
                <div class="w-2 h-2 mt-2 rounded-full bg-primary-500"></div>
                <div>
                    <div class="text-sm font-medium">{{ $item['label'] }}</div>
                    <div class="text-xs text-gray-500">{{ $item['user'] ?? 'System' }} • {{ $item['time'] }}</div>
                </div>
            </li>
            @empty
            <li class="text-sm text-gray-500">No recent activity.</li>
            @endforelse
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>