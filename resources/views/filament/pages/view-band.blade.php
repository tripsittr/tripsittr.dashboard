<?php
/** @var \App\Models\Band $record */
?>

<x-filament::page>
    <div class="flex flex-col lg:flex-row gap-6 w-full">
        <!-- Left: Main Content (Band Overview, Members, Albums, Songs) -->
        <div class="lg:basis-2/5! w-full grow-0 space-y-6">
            <!-- Band Overview -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Formation Date: {{ $record->formation_date ?? 'Unknown' }}
                </p>
                <p class="mt-2 text-gray-800 dark:text-gray-200">
                    {{ $record->description ?? 'No description available.' }}
                </p>
            </div>

            <!-- Singles -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Singles</h2>
                <ul class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($record->songs->whereNull('album_id') as $song)
                        <li class="px-4 py-3 flex justify-between items-center">
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $song->song_name }}</span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $song->duration ?? 'Unknown length' }}</span>
                        </li>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No singles available.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Albums -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Albums</h2>
                <div class="mt-2 space-y-3">
                    @forelse ($record->albums as $album)
                        <div x-data="{ expanded: false }" class="border dark:border-gray-600 rounded-lg overflow-hidden">
                            <button @click="expanded = !expanded"
                                class="w-full flex justify-between items-center bg-gray-100 dark:bg-gray-700 px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <span>{{ $album->title }} ({{ $album->release_date ?? 'Unknown Release Date' }})</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transform transition-transform duration-200"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="expanded" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <ul class="py-2">
                                    @forelse ($album->songs as $song)
                                        <li class="px-4 py-2 flex justify-between">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $song->song_name }}</span>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $song->duration ?? 'Unknown length' }}</span>
                                        </li>
                                    @empty
                                        <li class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No songs in this album.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No albums available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Social Media Sidebar -->
        <div class="lg:basis-3/5! w-full flex-shrink-0 space-y-6">
            <!-- Genres -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Genres</h2>
                <div class="flex flex-wrap gap-2 mt-2">
                    @forelse ($record->genre ?? [] as $genre)
                        <span class="px-3 py-1 text-xs font-medium text-white bg-primary-500 dark:bg-primary-700 rounded-full">
                            {{ $genre }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No genres assigned.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contact & Social Media</h2>
                <ul class="mt-2 text-sm text-gray-800 dark:text-gray-200 space-y-2">
                    <li>Website: 
                        @if ($record->website)
                            <a href="{{ $record->website }}" class="text-blue-500 hover:underline" target="_blank">
                                {{ $record->website }}
                            </a>
                        @else
                            N/A
                        @endif
                    </li>
                    <li>Instagram: 
                        @if ($record->instagram)
                            <a href="https://instagram.com/{{ $record->instagram }}" class="text-blue-500 hover:underline" target="_blank">
                                {{ '@' . $record->instagram }}
                            </a>
                        @else
                            N/A
                        @endif
                    </li>
                    <li>Twitter/X: 
                        @if ($record->twitter)
                            <a href="https://twitter.com/{{ $record->twitter }}" class="text-blue-500 hover:underline" target="_blank">
                                {{ '@' . $record->twitter }}
                            </a>
                        @else
                            N/A
                        @endif
                    </li>
                    <li>Facebook: 
                        @if ($record->facebook)
                            <a href="https://facebook.com/{{ $record->facebook }}" class="text-blue-500 hover:underline" target="_blank">
                                {{ $record->facebook }}
                            </a>
                        @else
                            N/A
                        @endif
                    </li>
                    <li>YouTube: 
                        @if ($record->youtube)
                            <a href="https://youtube.com/{{ $record->youtube }}" class="text-blue-500 hover:underline" target="_blank">
                                {{ $record->youtube }}
                            </a>
                        @else
                            N/A
                        @endif
                    </li>
                    <li>Email: {{ $record->email ?? 'N/A' }}</li>
                    <li>Phone: {{ $record->phone ?? 'N/A' }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Merchandise Section -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Merchandise</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
            @forelse ($record->merchandise as $item)
                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-32 object-cover rounded-md">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-2">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $item->description ?? 'No description' }}</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">${{ number_format($item->price, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Stock: {{ $item->stock }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Type: {{ ucfirst($item->type) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">SKU: {{ $item->sku ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Weight: {{ $item->weight ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Dimensions: {{ $item->dimensions ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Material: {{ $item->material ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Colors: {{ implode(', ', $item->colors ?? []) }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No merchandise available.</p>
            @endforelse
        </div>
    </div>

    <!-- Band Members Table -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold rounded-lg text-gray-900 dark:text-gray-100">Band Members</h2>
        <div class="overflow-x-auto rounded-lg mt-2">
            <table class="w-full border-collapse rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-200">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-200">Roles</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-200">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($record->bandMembers ?? collect() as $member)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $member->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm flex flex-row gap-3 text-gray-900 dark:text-gray-100">
                                @foreach ($member->band_roles ?? collect() as $role)
                                    <x-filament::badge class="max-w-fit p-1 px-2" size="xs" color="primary">
                                        {{ $role }}
                                    </x-filament::badge>   
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $member->created_at->format('M d, Y') ?? 'Unknown' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                No members assigned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-filament::page>
