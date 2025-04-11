<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <audio controls class="shadow-none" width="100%">
        <source 
            src="{{ asset($getState()) }}"
            type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <style>
        audio {
            width: 100%;
            background-color: #c75c5c;
            border-radius: 5px;
        }

        audio::-webkit-media-controls-panel {
            background-color: #c75c5c;
            border-radius: 5px;
        }
        .background {
            background-color: #c75c5c;
        }
    </style>
    <x-filament::button wire:click="viewMetadata">View Metadata</x-filament::button>
</x-dynamic-component>
