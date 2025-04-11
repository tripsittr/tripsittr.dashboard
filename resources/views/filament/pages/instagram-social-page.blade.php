<x-filament-panels::page>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col items-center justify-center space-y-4">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Link Your Facebook Account</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Connect your Facebook account to view posts and analytics.
        </p>
        <a href="{{ route('facebook.link') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Link Facebook Account
        </a>
    </div>
</x-filament-panels::page>
