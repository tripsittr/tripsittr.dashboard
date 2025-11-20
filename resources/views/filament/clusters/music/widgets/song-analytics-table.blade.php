<x-filament::card>
    @php
    $rows = $this->getData()['rows'] ?? collect();
    @endphp

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="text-left">
                    <th class="p-2">Name</th>
                    <th class="p-2">Streams</th>
                    <th class="p-2">Streams %</th>
                    <th class="p-2">Streams change</th>
                    <th class="p-2">Streams change %</th>
                    <th class="p-2">Downloads</th>
                    <th class="p-2">Imported</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr class="border-t">
                    <td class="p-2">{{ $row->name }}</td>
                    <td class="p-2">{{ number_format($row->streams) }}</td>
                    <td class="p-2">{{ $row->streams_pct !== null ? $row->streams_pct . '%' : '-' }}</td>
                    <td class="p-2">{{ $row->streams_change ?? 0 }}</td>
                    <td class="p-2">{{ $row->streams_change_pct !== null ? $row->streams_change_pct . '%' : '-' }}</td>
                    <td class="p-2">{{ number_format($row->downloads) }}</td>
                    <td class="p-2">{{ optional($row->imported_at)->toDateTimeString() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-2 text-sm text-gray-500">No data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rows->links() }}
    </div>
</x-filament::card>