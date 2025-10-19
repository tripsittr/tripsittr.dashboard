@php
$metadata = $metadata ?? [];
@endphp
<div class="space-y-4">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Audio Metadata</h3>
    @if(empty($metadata))
    <p class="text-sm text-gray-500 dark:text-gray-400">No metadata available for this audio file.</p>
    @else
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
        @foreach($metadata as $key => $value)
        <div>
            <dt class="font-medium text-gray-700 dark:text-gray-300">{{ Str::headline($key) }}</dt>
            <dd class="mt-0.5 text-gray-600 dark:text-gray-400 break-all">{{ is_scalar($value) ? $value :
                json_encode($value) }}</dd>
        </div>
        @endforeach
    </dl>
    @endif
</div>