<x-filament::modal id="shareVenueModal" :visible="true" close-on-click-away>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-primary-500 dark:text-primary-300">Share Venue</h2>
    </x-slot>

    <form method="POST" action="{{ route('venue.share', $venue->id) }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-100">Information to Share</label>
            <div class="mt-2 space-y-2">
                <div>
                    <input type="checkbox" name="info[]" value="name" id="info_name" class="text-primary-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded focus:ring-primary-500">
                    <label for="info_name" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Name</label>
                </div>
                <div>
                    <input type="checkbox" name="info[]" value="address" id="info_address" class="text-primary-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded focus:ring-primary-500">
                    <label for="info_address" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Address</label>
                </div>
                <div>
                    <input type="checkbox" name="info[]" value="city" id="info_city" class="text-primary-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded focus:ring-primary-500">
                    <label for="info_city" class="ml-2 text-sm text-gray-700 dark:text-gray-300">City</label>
                </div>
                <div>
                    <input type="checkbox" name="info[]" value="state" id="info_state" class="text-primary-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded focus:ring-primary-500">
                    <label for="info_state" class="ml-2 text-sm text-gray-700 dark:text-gray-300">State</label>
                </div>
                <div>
                    <input type="checkbox" name="info[]" value="zip" id="info_zip" class="text-primary-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded focus:ring-primary-500">
                    <label for="info_zip" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Zip</label>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" @click="showModal = false" class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-300 rounded-md">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md">Send</button>
        </div>
    </form>
</x-filament::modal>
