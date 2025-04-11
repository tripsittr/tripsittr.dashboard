<x-filament-widgets::widget>
    <x-filament::section>
        {{-- filepath: resources/views/filament/widgets/recent-records-widget.blade.php --}}
        <div>
            <h3 class="text-lg font-bold">Recent Records</h3>
            <ul>
                @forelse ($records as $record)
                    <li>
                        {{ $record->model_type }} (ID: {{ $record->model_id }}) - Viewed at {{ $record->created_at }}
                    </li>
                @empty
                    <li>No recent records found.</li>
                @endforelse
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
