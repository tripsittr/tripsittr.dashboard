<x-filament::page>
    <div class="space-y-6 p-6">
        <h2 class="text-2xl font-semibold">Social Connections</h2>

        <p class="text-sm text-gray-600">Connect social accounts so the app can read posts and publish on your behalf.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            @php
            $providers = ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'Twitter'];
            @endphp

            @foreach ($providers as $key => $label)
            <div class="border rounded p-4 flex items-center justify-between">
                <div>
                    <div class="font-medium">{{ $label }}</div>
                    <div class="text-sm text-gray-500">Link {{ $label }} to import posts and publish.</div>
                </div>

                <div>
                    @if (in_array($key, $connected))
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('social.unlink', ['provider' => $key]) }}">
                            @csrf
                            <button type="submit" class="filament-button secondary">Unlink</button>
                        </form>

                        @if (in_array($key, ['facebook', 'instagram']))
                        @php $acctId = $connectedAccounts[$key] ?? null; @endphp
                        @if ($acctId)
                        <form method="POST" action="{{ route('social.discover', ['id' => $acctId]) }}">
                            @csrf
                            <button type="submit" class="filament-button">Discover Page/IG</button>
                        </form>
                        @endif
                        @endif
                    </div>
                    @else
                    <a href="{{ route('social.link', ['provider' => $key]) }}"
                        class="filament-button primary">Connect</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-filament::page>