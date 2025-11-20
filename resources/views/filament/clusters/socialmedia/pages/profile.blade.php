<x-filament::page>
    <div class="p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Social Feed</h1>

            <div>
                <form method="GET">
                    <select name="provider" onchange="this.form.submit()" class="border rounded px-2 py-1">
                        <option value="facebook" {{ $provider==='facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="instagram" {{ $provider==='instagram' ? 'selected' : '' }}>Instagram</option>
                        <option value="twitter" {{ $provider==='twitter' ? 'selected' : '' }}>Twitter</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-sm text-gray-600">Showing posts for <strong>{{ ucfirst($provider) }}</strong>. (No external
                fetch implemented yet.)</p>

            <div class="mt-4 border rounded p-4">
                <p class="text-gray-500">When social integrations are linked, posts from the selected provider will
                    appear here.</p>
            </div>
        </div>
    </div>
</x-filament::page>