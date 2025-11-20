@php
// Minimal preview renderer for layout sections. This uses the same
// rendering approach as the public TripLink view but simplified for
// fast previews.
use App\Support\HtmlSanitizer;

$useLayout = !empty($layout) && isset($layout[0]['type']);
$avatarSize = intval($design['avatar_size'] ?? 120);

// Normalize fonts array (may be storage paths)
$fonts = is_array($fonts ?? null) ? $fonts : [];
@endphp
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @if( !empty($fonts)) @foreach($fonts as $f) @php $u =$f; // expecting storage path like 'triplinks/.../fonts/file.woff2'
        $url =str_starts_with($u, 'http') ? $u : asset('storage/' .$u);
        $fontName =pathinfo($u, PATHINFO_FILENAME);

        @endphp @font-face {
            font-family: '{{ $fontName }}';
            src: url('{{ $url }}') format('woff2');
            font-weight: normal;
            font-style: normal;
        }

        @endforeach @endif body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            margin: 0;
            padding: 16px;
            background: #f8fafc;
            color: #0f172a
        }

        .tl-wrap {
            max-width: 720px;
            margin: 0 auto;
        }

        .tl-title h1 {
            margin: 0;
            font-size: 1.5rem
        }

        .tl-links {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .tl-link {
            padding: 10px 12px;
            border-radius: 8px;
            background: #fff;
            color: #0f172a;
            text-decoration: none;
            display: inline-block
        }

        .tl-avatar {
            width: {
                    {
                    $avatarSize
                }
            }

            px;

            height: {
                    {
                    $avatarSize
                }
            }

            px;
            border-radius:9999px;
            overflow:hidden
        }

        img {
            max-width: 100%;
            height: auto
        }
    </style>
</head>

<body>
    <div class="tl-wrap">
        <div class="tl-title">
            <h1>{{ $title }}</h1>
            @if(!empty($bio))<p style="color:rgba(15,23,42,0.7)">{!! nl2br(e($bio)) !!}</p>@endif
        </div>

        @if($useLayout)
        @foreach($layout as $section)
        @php
        $type = $section['type'] ?? 'title';
        $padding = intval($section['padding'] ?? 12);
        $spacing = intval($section['spacing'] ?? 12);
        @endphp

        @if($type === 'title')
        <div style="padding:{{ $padding }}px 0; text-align:{{ $section['alignment'] ?? 'center' }};">
            <h2
                style="font-size:{{ intval($section['title_size'] ?? $section['font_size'] ?? 24) }}px; font-weight:{{ intval($section['title_weight'] ?? $section['font_weight'] ?? 700) }}; color:{{ $section['title_color'] ?? $section['color'] ?? '#0f172a' }}">
                {{ $section['title'] ?? '' }}</h2>
        </div>

        @elseif($type === 'paragraph')
        <div style="padding:{{ $padding }}px 0; text-align:{{ $section['alignment'] ?? 'center' }};">
            <div
                style="max-width:720px;margin:0 auto;color:{{ $section['paragraph_color'] ?? $section['color'] ?? 'rgba(15,23,42,0.85)' }}; font-size:{{ intval($section['paragraph_size'] ?? 16) }}px;">
                {!! HtmlSanitizer::sanitizeHtml($section['paragraph'] ?? '') !!}</div>
        </div>

        @elseif($type === 'image')
        @php
        $img = $section['image'] ?? null;
        $radius = intval($section['image_radius'] ?? $section['radius'] ?? 8);
        $w = $section['image_width'] ?? $section['width'] ?? '100%';
        $h = $section['image_height'] ?? $section['height'] ?? 'auto';
        @endphp
        <div style="padding:{{ $padding }}px 0; text-align:{{ $section['alignment'] ?? 'center' }};">
            <img src="{{ $img }}"
                style="border-radius:{{ $radius }}px; width:{{ $w }}; height:{{ $h }}; object-fit:cover;" />
        </div>

        @elseif($type === 'gallery')
        @php $imgs = is_array($section['gallery_images'] ?? null) ? $section['gallery_images'] : []; $radius =
        intval($section['image_radius'] ?? 8); $cols = intval($section['gallery_columns'] ?? 3); @endphp
        <div
            style="padding:{{ $padding }}px 0; display:grid; grid-template-columns:repeat({{ max(1,$cols) }},1fr); gap:8px;">
            @foreach(array_slice($imgs,0,9) as $img)
            <img src="{{ $img }}" style="width:100%;height:120px;object-fit:cover;border-radius:{{ $radius }}px;" />
            @endforeach
        </div>

        @elseif($type === 'button')
        @php $label = $section['button_label'] ?? $section['label'] ?? 'Click'; $url = $section['button_url'] ??
        $section['url'] ?? '#'; $radius = intval($section['button_radius'] ?? $section['radius'] ?? 8); $size =
        $section['button_size'] ?? $section['size'] ?? 'medium'; $useImage = (bool) ($section['use_image'] ?? false);
        $img = $section['image'] ?? null; $imgW = $section['image_width'] ?? 'auto'; $imgH = $section['image_height'] ??
        'auto'; @endphp
        <div style="padding:{{ $padding }}px 0; text-align:{{ $section['alignment'] ?? 'center' }};">
            @if($useImage && $img)
            <a href="{{ $url }}"><img src="{{ $img }}"
                    style="width:{{ $imgW }};height:{{ $imgH }};object-fit:cover;border-radius:{{ $radius }}px;" /></a>
            @else
            <a href="{{ $url }}" class="tl-link"
                style="padding:{{ $size === 'large' ? '14px 20px' : ($size === 'small' ? '8px 10px' : '10px 14px') }}; border-radius:{{ $radius }}px;">{{
                $label }}</a>
            @endif
        </div>

        @elseif($type === 'spacer')
        <div style="height:{{ intval($section['spacer_height'] ?? $section['height'] ?? 16) }}px"></div>
        @endif
        @endforeach
        @else
        <div class="tl-links">
            <div class="tl-link">No sections configured</div>
        </div>
        @endif
    </div>
</body>

</html>