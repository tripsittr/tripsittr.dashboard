{{-- filepath: /c:/Users/blaze/Documents/GitHub/tripsittr.dashboard/resources/views/filament/components/infolists/array-entry.blade.php --}}
<div>
    @if ($getLabel())
        <h4 class="text-md font-medium">{{ $getLabel() }}</h4>
    @endif

    @if ($getState())
        <ul class="text-sm font-normal">
            @foreach ($getState() as $item)
                <li>{{ implode('', $item) }}</li>
            @endforeach
        </ul>
    @else
        <p class="text-sm font-normal">No data available.</p>
    @endif
</div>
