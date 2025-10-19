@php($childComponents = $getChildComponents())
<div {{ $attributes->class([
    'filament-stack flex flex-col gap-4',
    ]) }}>
    @foreach($childComponents as $component)
    {{ $component }}
    @endforeach
</div>