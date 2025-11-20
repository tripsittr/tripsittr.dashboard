@php
$route = route('music.song-analytics.import');
@endphp

<x-filament::page>
    <form method="POST" action="{{ $route }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-filament::card>
                    <label class="block text-sm font-medium text-gray-700">Upload CSV file</label>
                    <input type="file" name="csv_file" accept=".csv" required class="mt-1" />

                    <div class="mt-4">
                        <x-filament::button type="submit">Import CSV</x-filament::button>
                    </div>
                </x-filament::card>
            </div>
        </div>
    </form>

    @if(session('import_result'))
    <x-filament::card class="mt-6">
        <h3 class="text-lg font-medium">Import result</h3>
        <pre class="mt-2">{{ session('import_result') }}</pre>
    </x-filament::card>
    @endif
</x-filament::page>