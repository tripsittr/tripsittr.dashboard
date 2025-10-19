@php
/** @var \App\Models\Song|null $record */
// Resolve the current record from the Infolist context and guard against Closures
$record = $record ?? (isset($getRecord) && is_callable($getRecord) ? $getRecord() : null);
if ($record instanceof \Closure) { $record = $record(); }

if (! $record) {
return; // nothing to render
}

$normNames = function ($value) {
$arr = is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []);
if (is_array($arr) && !empty($arr) && is_array($arr[0] ?? null) && array_key_exists('name', $arr[0])) {
$arr = array_map(fn ($row) => $row['name'], $arr);
}
return collect($arr)->filter()->map(fn($v) => is_array($v) ? ($v['name'] ?? '') : (string) $v)->filter()->values();
};

$primary = $normNames($record->primary_artists ?? []);
$featured = $normNames($record->featured_artists ?? []);
$producers = $normNames($record->producers ?? []);
$composers = $normNames($record->composers ?? []);

// Fallback to raw metadata 'artist' if no primary artists; handle JSON/string gracefully
$rawMeta = is_array($record->raw_metadata ?? null)
? $record->raw_metadata
: (json_decode($record->raw_metadata ?? '', true) ?: []);

$artists = $primary->isNotEmpty()
? $primary->implode(', ')
: (data_get($rawMeta, 'artist') ?: '');

$duration = is_numeric($record->duration ?? null)
? sprintf('%d:%s', floor($record->duration/60), str_pad((string)($record->duration%60),2,'0',STR_PAD_LEFT))
: null;
@endphp

<div class="flex flex-col gap-1">
    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $record->title }}</div>
    @if($artists)
    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $artists }}</div>
    @endif

    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
        @if($record->genre)
        <span
            class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">{{
            $record->genre }}</span>
        @endif
        @if($record->subgenre)
        <span>•</span>
        <span>{{ $record->subgenre }}</span>
        @endif
        @if($record->release_date)
        <span>•</span>
        <span>{{ $record->release_date?->format('M d, Y') }}</span>
        @endif
        @if($duration)
        <span>•</span>
        <span>{{ $duration }}</span>
        @endif
        @if($record->status)
        <span>•</span>
        <span
            class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">{{
            ucfirst($record->status) }}</span>
        @endif
        @if($record->visibility)
        <span>•</span>
        <span
            class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">{{
            ucfirst($record->visibility) }}</span>
        @endif
        @if($record->distribution_status)
        <span>•</span>
        <span
            class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">{{
            ucfirst($record->distribution_status) }}</span>
        @endif
    </div>

    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
        @if($record->isrc)
        <div><span class="text-gray-500 dark:text-gray-400">ISRC:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->isrc }}</span></div>
        @endif
        @if($record->upc)
        <div><span class="text-gray-500 dark:text-gray-400">UPC:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->upc }}</span></div>
        @endif
        @if($record->track_number)
        <div><span class="text-gray-500 dark:text-gray-400">Track #:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->track_number }}@if($record->track_total)/{{
                $record->track_total }}@endif</span></div>
        @endif
        @if($record->disc_number)
        <div><span class="text-gray-500 dark:text-gray-400">Disc #:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->disc_number }}@if($record->disc_total)/{{
                $record->disc_total }}@endif</span></div>
        @endif
        @if($record->bitrate)
        <div><span class="text-gray-500 dark:text-gray-400">Bitrate:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->bitrate }} kbps</span></div>
        @endif
        @if($record->sample_rate)
        <div><span class="text-gray-500 dark:text-gray-400">Sample Rate:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ number_format($record->sample_rate) }} Hz</span></div>
        @endif
        @if($record->channels)
        <div><span class="text-gray-500 dark:text-gray-400">Channels:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->channels }}</span></div>
        @endif
        @if($record->codec)
        <div><span class="text-gray-500 dark:text-gray-400">Codec:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ strtoupper($record->codec) }}</span></div>
        @endif
        @if($record->format)
        <div><span class="text-gray-500 dark:text-gray-400">Format:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ strtoupper($record->format) }}</span></div>
        @endif
        @if($record->mime_type)
        <div><span class="text-gray-500 dark:text-gray-400">MIME:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->mime_type }}</span></div>
        @endif
        @if($record->file_size)
        <div><span class="text-gray-500 dark:text-gray-400">File Size:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $record->file_size }} MB</span></div>
        @endif
        @if($record->song_file)
        <div class="sm:col-span-2 lg:col-span-1"><span class="text-gray-500 dark:text-gray-400">File:</span> <span
                class="text-gray-800 dark:text-gray-200 break-all">{{ $record->song_file }}</span></div>
        @endif
    </div>

    @if($featured->isNotEmpty() || $producers->isNotEmpty() || $composers->isNotEmpty())
    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
        @if($featured->isNotEmpty())
        <div><span class="text-gray-500 dark:text-gray-400">Featured:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $featured->implode(', ') }}</span></div>
        @endif
        @if($producers->isNotEmpty())
        <div><span class="text-gray-500 dark:text-gray-400">Producers:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $producers->implode(', ') }}</span></div>
        @endif
        @if($composers->isNotEmpty())
        <div><span class="text-gray-500 dark:text-gray-400">Composers:</span> <span
                class="text-gray-800 dark:text-gray-200">{{ $composers->implode(', ') }}</span></div>
        @endif
    </div>
    @endif
</div>