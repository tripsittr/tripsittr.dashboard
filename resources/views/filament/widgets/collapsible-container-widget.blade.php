<x-filament-widgets::widget>
    <x-filament::section collapsible collapsed persist-collapsed id="user-details">
        <div>
            {{-- Dashboard calendar removed here to avoid mounting a FullCalendar Livewire
            widget on pages where it isn't needed (it declares union-typed public
            properties which some Livewire versions can't serialize). The calendar
            is still included on the Dashboard page via its widget registration.
            --}}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>