<x-filament::page>
    <div class="max-w-5xl mx-auto p-6">
        
        <fb:login-button 
        config_id="{config_id}"
        onlogin="checkLoginState();">
        </fb:login-button>
        
        <h1 class="text-2xl font-bold mb-4">Instagram Analytics</h1>
        @if ($userData)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p><strong>Username:</strong> {{ $userData['username'] }}</p>
                <p><strong>Account Type:</strong> {{ $userData['account_type'] }}</p>
                <p><strong>Media Count:</strong> {{ $userData['media_count'] }}</p>
            </div>
        @else
            <p class="text-red-500">No data available. Please ensure you are logged in to Instagram and try again.</p>
        @endif

        @if (!empty($insights))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Insights</h2>
                <ul class="list-disc list-inside text-gray-700 dark:text-white space-y-2">
                    @foreach ($insights as $insight)
                        <li>
                            <strong>{{ ucfirst($insight['name']) }}:</strong> {{ $insight['values'][0]['value'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-red-500">No insights data available. Please ensure you are logged in to Instagram and try again.</p>
        @endif
    </div>
</x-filament::page>
