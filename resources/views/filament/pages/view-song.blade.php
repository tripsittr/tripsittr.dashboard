<x-filament::page>
    <div>
        <div class="px-8 sm:px-0">
            <h3 class="text-base/7 font-semibold text-gray-300">Song Information</h3>
        </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Audio Metadata</h3>
                <ul class="mt-2 text-sm text-gray-800 dark:text-gray-200 space-y-2">
                    <li><strong>Duration:</strong> {{ gmdate('i:s', $record->duration ?? 0) }}</li>
                    <li><strong>Bitrate:</strong> {{ $record->bitrate ?? 'Unknown' }}</li>
                    <li><strong>Sample Rate:</strong> {{ $record->sample_rate ?? 'Unknown' }} Hz</li>
                    <li><strong>Codec:</strong> {{ $record->codec ?? 'Unknown' }}</li>
                    <li><strong>Format:</strong> {{ $record->format ?? 'Unknown' }}</li>
                    <li><strong>Channels:</strong> {{ $record->channels ?? 'Unknown' }}</li>
                </ul>
            </div>

        @if ($record->artwork)
            <img src="{{ $record->artwork }}" alt="{{ $record->title }}" class="w-full h-96 object-cover">
        @endif
            <div class="mt-6 px-8 sm:px-0">
            <dl class="grid grid-cols-1 sm:grid-cols-3">
            <div class="border-t border-gray-600 px-4 py-6 sm:col-span-1 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Song name</dt>
                <dd class="mt-1 text-sm/6 text-gray-400 sm:mt-2">{{ $record->title }}</dd>
            </div>
            <div class="border-t border-gray-600 px-4 py-6 sm:col-span-1 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Application for</dt>
                <dd class="mt-1 text-sm/6 text-gray-400 sm:mt-2">Backend Developer</dd>
            </div>
            <div class="border-t border-gray-600 px-4 py-6 sm:col-span-1 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Email address</dt>
                <dd class="mt-1 text-sm/6 text-gray-400 sm:mt-2">margotfoster@example.com</dd>
            </div>
            <div class="border-t border-gray-600 px-4 py-6 sm:col-span-1 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">Salary expectation</dt>
                <dd class="mt-1 text-sm/6 text-gray-400 sm:mt-2">$120,000</dd>
            </div>
            <div class="border-t border-gray-600 px-4 py-6 sm:col-span-2 sm:px-0">
                <dt class="text-sm/6 font-medium text-gray-900">About</dt>
                <dd class="mt-1 text-sm/6 text-gray-400 sm:mt-2">Fugiat ipsum ipsum deserunt culpa aute sint do nostrud anim incididunt cillum culpa consequat. Excepteur qui ipsum aliquip consequat sint. Sit id mollit nulla mollit nostrud in ea officia proident. Irure nostrud pariatur mollit ad adipisicing reprehenderit deserunt qui eu.</dd>
            </div>
            </dl>
        </div>
    </div>
</x-filament::page>
