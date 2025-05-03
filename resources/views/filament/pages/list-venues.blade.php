@php
    $venues = App\Models\Venue::all();
    $tenant_id = Auth::user()->team_id;
    $fields = [
        'name', 'address_1', 'address_2', 'country', 'city', 'state', 'zip', 'lat', 'lng', 'phone', 'email', 'url', 'catagories', 'capacity', 'indoor_outdoor', 'stage_size', 'seating_type', 'parking_info', 'age_restriction', 'alcohol_policy', 'booking_contact_name', 'booking_email', 'booking_phone', 'booking_website', 'rental_price_range', 'sound_equipment_provided', 'backline_available', 'lighting_equipment_provided', 'green_room', 'wifi_available', 'wheelchair_accessible', 'food_beverage_available', 'public_transit_access', 'nearby_hotels', 'notes', 'facebook_profile', 'instagram_handle', 'linkedin', 'twitter', 'whatsapp', 'youtube', 'tiktok', 'has_backstage', 'climate_control', 'bag_policy', 'restroom_info', 'ticket_types', 'ticket_policy', 'bo_address_1', 'bo_address_2', 'bo_city', 'bo_state', 'bo_zip', 'bo_country', 'bo_phone', 'bo_email', 'bo_url', 'bo_hours', 'bo_notes', 'info_url'
    ];
@endphp

<x-filament-panels::page>
    <div class="container mx-auto">
        <x-filament::grid
            style="--cols-md: repeat(4, minmax(0, 1fr));"
            class="gap-8 md:grid-cols-[--cols-md]"
        >
            @foreach ($venues as $venue)
                <x-filament::card class="bg-white dark:bg-gray-800" style="box-shadow:;">
                    <div class="flex justify-between items-start">
                        <h2 class="text-xl font-bold mb-4 text-primary-500 dark:text-primary-300">
                            <a href="{{ route('filament.admin.knowledge.resources.venues.view', ['tenant' => $tenant->id, 'record' => $venue->id]) }}" class="text-primary-500 dark:text-primary-300 hover:underline">
                                {{ $venue->name }}
                            </a>
                        </h2>
                        <div x-data="{ open: false, showModal: false }" class="relative">
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                                <ul class="py-1 rounded-md bg-white dark:bg-gray-800">
                                    <li>
                                        <a href="{{ route('filament.admin.knowledge.resources.venues.edit', ['tenant' => $tenant->id, 'record' => $venue->id]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-white no-underline hover:underline">Edit</a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('venue.destroy', $venue->id) }}" onsubmit="return confirm('Are you sure you want to delete this venue?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white no-underline hover:underline">Delete</button>
                                        </form>
                                    </li>
                                    <li>
                                        <x-filament::modal id="shareVenueModal" :visible="true" :close-by-clicking-away="true" :close-by-escaping="true" slide-over width="4xl">
                                            <x-slot name="trigger" class="w-full">
                                                <button type="button" @click="showModal = true" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white no-underline hover:underline">Email</button>
                                            </x-slot>
                                            <x-slot name="heading">
                                                <h2 class="text-xl font-bold text-primary-500 dark:text-primary-300">Share Venue</h2>
                                            </x-slot>

                                            <form method="POST" action="{{ route('venue.share', $venue->id) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                                    <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                                                </div>
                                                <div class="mb-1 w-full max-h-96 mt-6 overflow-y-auto">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Information to Share</label>
                                                    <div class="mt-2 w-full flex flex-wrap gap-3">
                                                        <x-filament::grid
                                                            style="--cols-md: repeat(3, minmax(0, 2fr));"
                                                            class="gap-3 md:grid-cols-[--cols-md]"
                                                        >
                                                            @foreach($fields as $field)
                                                                <div class="flex gap-2 items-center w-1/3">
                                                                    <input type="checkbox" name="info[]" value="{{ $field }}" id="info_{{ $field }}" class="m-3 p-2 text-primary-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded focus:ring-primary-500">
                                                                    <label for="info_{{ $field }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                                                </div>
                                                            @endforeach
                                                        </x-filament::grid>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end">
                                                    <button type="button" @click="showModal = false" class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md">Send</button>
                                                </div>
                                            </form>
                                        </x-filament::modal>
                                    </li>
                                    <li>
                                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white no-underline hover:underline">Petition</button>
                                    </li>
                                    @if($venue->url)
                                        <li>
                                            <a href="{{ $venue->url }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 dark:text-white no-underline hover:underline">Website</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1 text-md">
                        @if($venue->address_1)
                            <p class="dark:text-gray-300">{{ $venue->address_1 }}</p>
                        @endif
                        @if($venue->address_2)
                            <p class="dark:text-gray-300">{{ $venue->address_2 }}</p>
                        @endif
                        <p class="dark:text-gray-300">{{ $venue->city }}
                            @if($venue->state && $venue->city),@endif {{ $venue->state }} {{ $venue->zip }}</p>
                    </div>
                </x-filament::card>
            @endforeach
        </x-filament::grid>
    </div>
</x-filament-panels::page>
