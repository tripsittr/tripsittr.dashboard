@php
    use App\Models\Knowledge;
    $faqs = Knowledge::where('type', 'faq')->get();
@endphp


<x-filament-panels::page>
    @foreach($faqs as $key => $value)
        @if($value->status == 'Published')
            <x-filament::section
                collapsible
                collapsed
            >
                <x-slot name="heading">
                    Q: {{ $value->title }}
                </x-slot>
                <div class="mb-4">
                    <p>A: {{ $value->description }}</p>
                </div>
            </x-filament::section>
        @endif
    @endforeach
</x-filament-panels::page>
