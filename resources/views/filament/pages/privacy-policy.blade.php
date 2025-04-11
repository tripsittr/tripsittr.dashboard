<x-filament-panels::page>
    @php
        $markdown = file_get_contents(config_path('markdown/privacy-policy.md'));
        $parsedMarkdown = (new Parsedown())->text($markdown);
    @endphp

    <div class="prose">
        {!! $parsedMarkdown !!}
    </div>
</x-filament-panels::page>
