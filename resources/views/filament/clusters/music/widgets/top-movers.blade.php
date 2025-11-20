<div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg p-3">
    <div class="flex items-center justify-between">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-100">Top Movers</h4>
        <span class="text-xs text-gray-500 dark:text-gray-400">Latest import</span>
    </div>

    <div class="mt-3">
        <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400">Top gainers</h5>
        <ul class="mt-2 divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($this->getData()['gainers'] as $g)
            <li class="py-2 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-primary-50 dark:bg-primary-900 rounded flex items-center justify-center text-primary-700 dark:text-primary-300 font-semibold text-sm">
                        @php
                        $parts = preg_split('/\s+/', trim($g->name));
                        $initials = '';
                        foreach (array_slice($parts, 0, 2) as $p) {
                        $initials .= mb_substr($p, 0, 1);
                        }
                        @endphp
                        {{ $initials }}</div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium truncate text-gray-800 dark:text-gray-100">{{ $g->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($g->streams ?? 0) }}
                            streams</div>
                    </div>
                </div>
                <div class="text-sm font-medium">
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ ($g->streams_change ?? 0) >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ ($g->streams_change ?? 0) >= 0 ? '+' : '' }}{{ number_format($g->streams_change ?? 0) }}
                    </span>
                </div>
            </li>
            @empty
            <li class="py-2 text-sm text-gray-500">No data</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400">Top losers</h5>
        <ul class="mt-2 divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($this->getData()['losers'] as $g)
            <li class="py-2 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-gray-50 dark:bg-gray-700 rounded flex items-center justify-center text-gray-600 dark:text-gray-200 font-semibold text-sm">
                        @php
                        $parts = preg_split('/\s+/', trim($g->name));
                        $initials = '';
                        foreach (array_slice($parts, 0, 2) as $p) {
                        $initials .= mb_substr($p, 0, 1);
                        }
                        @endphp
                        {{ $initials }}</div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium truncate text-gray-800 dark:text-gray-100">{{ $g->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($g->streams ?? 0) }}
                            streams</div>
                    </div>
                </div>
                <div class="text-sm font-medium">
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ ($g->streams_change ?? 0) >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ ($g->streams_change ?? 0) >= 0 ? '+' : '' }}{{ number_format($g->streams_change ?? 0) }}
                    </span>
                </div>
            </li>
            @empty
            <li class="py-2 text-sm text-gray-500">No data</li>
            @endforelse
        </ul>
    </div>
</div>