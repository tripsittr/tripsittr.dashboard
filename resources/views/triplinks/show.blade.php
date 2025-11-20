<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $trip->title ?? 'Profile' }}</title>
    <link href="/css/app.css" rel="stylesheet">
    <style>
        :root {
            --tl-bg: #000000;
            --tl-text: #f7f7f7;
            --tl-accent: #555555;
            --tl-avatar-size: 120px;
            /* default, can be overridden inline on .tl-wrap */
            --tl-hero-height: 200px;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: var(--tl-text);
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
        }

        .tl-avatar {
            margin-top: 20px;
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: var(--tl-avatar-size);
            height: var(--tl-avatar-size);
            border-radius: 9999px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(2, 6, 23, 0.12);
            background: var(--tl-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 220ms cubic-bezier(.2, .9, .2, 1), box-shadow 220ms ease, width 220ms ease, height 220ms ease;
        }

        /* Banner transition (subtle) */
        .tl-banner {
            transition: transform 320ms ease, opacity 240ms ease;
            will-change: transform, opacity;
        }

        /* Respect reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {

            .tl-avatar,
            .tl-banner {
                transition: none !important;
            }
        }

        .tl-banner {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .tl-meta {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        /* center the avatar within the hero regardless of sizes */
        header.tl-hero {
            position: relative;
            height: var(--tl-hero-height);
            min-height: 80px;
            overflow: hidden;
            z-index: 5;
            box-shadow: 0px -7px 15px 9px #0000006a;
        }

        .tl-avatar {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: var(--tl-avatar-size);
            height: var(--tl-avatar-size);
            border-radius: 9999px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(2, 6, 23, 0.12);
            background: var(--tl-accent);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tl-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .tl-content {
            padding-top: calc(var(--tl-hero-height) / 10 + var(--tl-avatar-size) / 10);
            max-width: 900px;
            margin: 0 auto;
            padding-left: 16px;
            padding-right: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Responsive: scale avatar down on small screens and reduce hero height slightly */
        @media (max-width: 640px) {
            .tl-wrap {
                --tl-avatar-size: calc(var(--tl-avatar-size) * 0.6);
                --tl-hero-height: calc(var(--tl-hero-height) * 0.8);
            }

            .tl-title h1 {
                font-size: 1.25rem;
            }

            .tl-content {
                padding-left: 12px;
                padding-right: 12px;
            }
        }

        .tl-wrap {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            width: 100%;
            position: relative;
        }

        /* Desktop: show a phone mock/frame around the tl-wrap */
        .tl-outer {
            display: block;
            padding: 24px;
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            body {
                background: #e6eef6;
                /* soft background around phone */
            }

            .tl-outer {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 48px;
                position: relative;
            }

            /* decorative photorealistic phone frame sits on top of the screen content */
            .phone-wrapper {
                width: 386.1px;
                height: 835.56px;
                position: relative;
                display: block;
            }

            /* the image frame itself should sit above the screen content so the transparent screen masks the content */
            .phone-frame {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                z-index: 3;
                width: 100%;
                height: 100%;
                pointer-events: none;
                display: block;
                object-fit: contain;
            }

            .tl-wrap {
                /* center the screen content directly behind the frame */
                width: 336px;
                height: 726px;
                border-radius: 35px;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                background: var(--tl-bg);
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                z-index: 2;
            }

            /* make phone sit slightly higher on very large screens */
            @media (min-width: 1500px) {
                .tl-outer {
                    align-items: flex-start;
                    padding-top: 48px;
                }
            }

            /* Ensure content inside the phone doesn't extend past the visual bezel top/bottom */
            .tl-wrap>* {
                box-sizing: border-box;
            }

            /* add subtle vignette behind the phone when wallpaper isn't present */
            .tl-outer:not([style*="background-image"])::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.06) 0%, rgba(0, 0, 0, 0.12) 60%);
                z-index: 0;
                pointer-events: none;
            }

            /* hide outer page scrollbar on desktop so the phone internal scroll is used */
            html,
            body,
            .tl-outer {
                height: 100vh;
                overflow: hidden;
            }
        }

        /* On small screens, make it full width and remove the phone frame */
        @media (max-width: 1023px) {
            .tl-wrap {
                width: 100% !important;
                min-height: 100vh !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            body {
                background: var(--tl-bg);
            }
        }

        .tl-title h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .tl-title p {
            margin: .25rem 0 0;
            color: var(--tl-text);
            opacity: 0.85;
        }

        /* Layout section styles (repeater rendering) */
        .tl-section {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-bottom: 12px;
            display: block;
        }

        .tl-section .tl-inner {
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .tl-title-section h2,
        .tl-paragraph-section p {
            color: var(--tl-text);
            margin: 0;
        }

        .tl-button {
            display: inline-block;
            width: auto;
            text-align: center;
            padding: 12px 16px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            color: #fff;
            background: var(--tl-accent);
        }

        .tl-button.alt {
            background: transparent;
            color: var(--tl-text);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .tl-image img {
            display: block;
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
        }

        .tl-gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        /* Wrapper to center buttons reliably (handles inline-block and fixed-width image-mode buttons) */
        .tl-button-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .tl-button.image-mode {
            display: block;
            box-sizing: border-box;
        }

        @media (max-width: 420px) {
            .tl-wrap {
                width: 100% !important;
                left: 0 !important;
                transform: none !important;
                top: 0 !important;
            }

            .tl-gallery {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

@php
$design = $trip->design ?? [];
$bg = $design['background_color'] ?? null;
$text = $design['text_color'] ?? null;
$accent = $design['accent_color'] ?? null;
$avatarSize = intval($design['avatar_size'] ?? 120);
$resolve = function($val) {
if (empty($val)) return null;
if (is_string($val) && preg_match('/^https?:\/\//i', $val)) return $val;
return asset('storage/' . ltrim($val, '/'));
};

$wallpaper = $resolve($design['wallpaper'] ?? null);

$bannerUrl = $resolve($trip->banner_image ?? null);
$avatarUrl = $resolve($trip->profile_image ?? null);

// Small helpers: detect light colors and produce a darker bezel color when needed
$hexToRgb = function (?string $hex) {
if (empty($hex)) return null;
$h = ltrim($hex, '#');
if (strlen($h) === 3) {
$h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
}
if (! preg_match('/^[0-9a-fA-F]{6}$/', $h)) return null;
return [hexdec(substr($h,0,2)), hexdec(substr($h,2,2)), hexdec(substr($h,4,2))];
};

$isLight = function (?string $hex) use ($hexToRgb) {
$rgb = $hexToRgb($hex);
if (! $rgb) return false;
// relative luminance approximation
[$r,$g,$b] = $rgb;
$l = (0.2126*$r + 0.7152*$g + 0.0722*$b) / 255;
return $l > 0.7; // fairly light
};

$darkenHex = function (?string $hex, float $amount = 0.35) use ($hexToRgb) {
$rgb = $hexToRgb($hex) ?: [15,23,42];
[$r,$g,$b] = $rgb;
$r = max(0, min(255, intval($r * (1 - $amount))));
$g = max(0, min(255, intval($g * (1 - $amount))));
$b = max(0, min(255, intval($b * (1 - $amount))));
return sprintf('#%02x%02x%02x', $r, $g, $b);
};

$bezel = $accent ? ($isLight($accent) ? $darkenHex($accent, 0.45) : $accent) : '#0f172a';

// External photorealistic frame asset (public storage)
$frameAsset = $resolve('iphone-template.png');
@endphp

<body style="background: #fff;">
    <div class="tl-outer"
        style="@if(
            $wallpaper
        ) background-image: url({{ e($wallpaper) }}); background-size: cover; background-position: center; @endif --tl-bg: {{ $bg ?? '#f8fafc' }}; --tl-text: {{ $text ?? '#0f172a' }}; --tl-accent: {{ $accent ?? '#f87171' }}; --tl-avatar-size: {{ $avatarSize }}px; --tl-hero-height: {{ intval($design['hero_height'] ?? 200) }}px;">
        <div class="phone-wrapper">
            @if($frameAsset)
            <img src="{{ $frameAsset }}" alt="iPhone frame" class="phone-frame" aria-hidden="true" />
            @endif

            <div class="tl-wrap"
                style="--tl-bg: {{ $bg ?? '#f8fafc' }}; --tl-text: {{ $text ?? '#0f172a' }}; --tl-accent: {{ $accent ?? '#f87171' }}; --tl-avatar-size: {{ $avatarSize }}px; --tl-hero-height: {{ intval($design['hero_height'] ?? 200) }}px; background: var(--tl-bg);">
                <header class="tl-hero" style="--tl-hero-height: {{ intval($design['hero_height'] ?? 200) }}px;"
                    role="banner">
                    @if($bannerUrl)
                    <img src="{{ $bannerUrl }}" alt="{{ e($trip->title ?? 'Banner') }}" class="tl-banner" />
                    @endif

                    @if($avatarUrl)
                    <div class="tl-avatar"><img src="{{ $avatarUrl }}" alt="{{ e($trip->title ?? 'Avatar') }}" /></div>
                    @else
                    <div class="tl-avatar" aria-hidden="true"
                        style="background:linear-gradient(135deg,var(--tl-accent),#7c3aed);color:#fff;font-weight:700;font-size:1.25rem;">
                        {{ strtoupper(substr($trip->title ?? 'U', 0, 1)) }}
                    </div>
                    @endif
                </header>

                <main class="tl-content" role="main">
                    <div class="tl-title">
                        <h1>{{ $trip->title }}</h1>
                        @if(!empty($trip->bio))
                        <p>{{ $trip->bio }}</p>
                        @endif
                    </div>
                    @php
                    $layout = $trip->layout ?? [];
                    if (! is_array($layout)) {
                    $layout = [];
                    }
                    @endphp

                    {{-- Render layout sections saved in the TripLink layout repeater --}}
                    @foreach($layout as $i => $section)
                    @php
                    $type = $section['type'] ?? 'title';
                    $padding = intval($section['padding'] ?? 12);
                    $spacing = intval($section['spacing'] ?? 12);
                    $bgColor = $section['background_color'] ?? 'transparent';
                    $align = $section['alignment'] ?? 'center';
                    $innerStyle = "text-align: {$align}; padding: {$padding}px; margin-bottom: {$spacing}px; background:
                    {$bgColor};";
                    @endphp

                    <section class="tl-section tl-{{ $type }}-section" style="{{ $innerStyle }}">
                        <div class="tl-inner">
                            @if($type === 'title')
                            <h2
                                style="color: {{ $section['title_color'] ?? 'var(--tl-text)' }}; font-size: {{ intval($section['title_size'] ?? 20) }}px; font-weight: {{ $section['title_weight'] ?? 700 }};">
                                {{ $section['title'] ?? '' }}</h2>
                            @elseif($type === 'paragraph')
                            <div class="tl-paragraph-section">
                                <p
                                    style="color: {{ $section['paragraph_color'] ?? 'var(--tl-text)' }}; font-size: {{ intval($section['paragraph_size'] ?? 16) }}px;">
                                    {!! nl2br(e($section['paragraph'] ?? '')) !!}</p>
                            </div>
                            @elseif($type === 'image')
                            @php $img = $resolve($section['image'] ?? null); @endphp
                            @if($img)
                            <div class="tl-image"
                                style="width: {{ $section['image_width'] ?? '100%' }}; height: {{ $section['image_height'] ?? 'auto' }};">
                                <img src="{{ $img }}" alt=""
                                    style="border-radius: {{ intval($section['image_radius'] ?? 0) }}px;" />
                            </div>
                            @endif
                            @elseif($type === 'gallery')
                            @php $imgs = $section['gallery_images'] ?? []; @endphp
                            @if(is_array($imgs) && count($imgs))
                            <div class="tl-gallery">
                                @foreach($imgs as $g)
                                @php $gUrl = $resolve($g); @endphp
                                @if($gUrl)
                                <div><img src="{{ $gUrl }}" alt="" /></div>
                                @endif
                                @endforeach
                            </div>
                            @endif
                            @elseif($type === 'button')
                            @php
                            $label = $section['button_label'] ?? 'Visit';
                            $url = $section['button_url'] ?? '#';
                            $radius = intval($section['button_radius'] ?? 12);
                            $bgMode = $section['button_bg_mode'] ?? 'color';
                            $btnStyle = "border-radius: {$radius}px;";

                            // Border settings (available when editing a button)
                            $borderWidth = intval($section['button_border_width'] ?? 0);
                            $borderStyle = $section['button_border_style'] ?? 'solid';
                            $borderColor = $section['button_border_color'] ?? null;
                            if ($borderWidth > 0) {
                            $color = $borderColor ?: ($text ?? '#0f172a');
                            $btnStyle .= "border: {$borderWidth}px {$borderStyle} {$color};";
                            } else {
                            // ensure no unexpected borders by default
                            $btnStyle .= "border: none;";
                            }

                            // Color mode
                            if ($bgMode === 'color') {
                            $btnBg = $section['button_color'] ?? null;
                            if ($btnBg) {
                            $btnStyle .= "background: {$btnBg};";
                            // auto-contrast: pick white text for dark bg, dark text for light bg
                            $btnTextColor = $section['button_text_color'] ?? ($isLight($btnBg) ? '#0f172a' : '#ffffff');
                            }
                            // keep default .tl-button text color unless overridden
                            } else {
                            // Image mode
                            $bgImg = $resolve($section['button_bg_image'] ?? null);
                            if ($bgImg) {
                            $btnStyle .= "background-image: url('{$bgImg}'); background-size: cover;
                            background-position: center; background-repeat: no-repeat;";
                            }
                            // Allow admins to set either bg-specific sizes (image mode) or general button sizes.
                            $btnWidth = $section['button_bg_width'] ?? $section['button_width'] ?? null;
                            $btnHeight = $section['button_bg_height'] ?? $section['button_height'] ?? null;
                            if (is_numeric($btnWidth) && $btnWidth > 0) {
                            // make the button a block-level element and center it when a fixed width is provided
                            $btnStyle .= "width: {$btnWidth}px; display: block; margin-left: auto; margin-right: auto;";
                            }
                            if (is_numeric($btnHeight) && $btnHeight > 0) {
                            $btnStyle .= "height: {$btnHeight}px; line-height: {$btnHeight}px;";
                            }
                            $textColor = $section['button_text_color'] ?? '#ffffff';
                            }
                            // Non-image mode: respect button_width/button_height if provided
                            if ($bgMode !== 'image') {
                            $btnWidth = $section['button_width'] ?? null;
                            $btnHeight = $section['button_height'] ?? null;
                            if (is_numeric($btnWidth) && $btnWidth > 0) {
                            $btnStyle .= "width: {$btnWidth}px; display: block; margin-left: auto; margin-right: auto;";
                            }
                            if (is_numeric($btnHeight) && $btnHeight > 0) {
                            $btnStyle .= "height: {$btnHeight}px; line-height: {$btnHeight}px;";
                            }
                            }

                            // Per-button padding (top/right/bottom/left) if provided via EdgeEditor
                            $btnPaddingArr = $section['button_padding'] ?? null;
                            if (is_array($btnPaddingArr)) {
                            $pt = intval($btnPaddingArr['top'] ?? 0);
                            $pr = intval($btnPaddingArr['right'] ?? 0);
                            $pb = intval($btnPaddingArr['bottom'] ?? 0);
                            $pl = intval($btnPaddingArr['left'] ?? 0);
                            $btnStyle .= "padding: {$pt}px {$pr}px {$pb}px {$pl}px;";
                            }

                            // Per-button margin (top/right/bottom/left) if provided via EdgeEditor
                            $btnMarginArr = $section['button_margin'] ?? null;
                            if (is_array($btnMarginArr)) {
                            $mt = intval($btnMarginArr['top'] ?? 0);
                            $mr = intval($btnMarginArr['right'] ?? 0);
                            $mb = intval($btnMarginArr['bottom'] ?? 0);
                            $ml = intval($btnMarginArr['left'] ?? 0);
                            $btnStyle .= "margin: {$mt}px {$mr}px {$mb}px {$ml}px;";
                            }
                            @endphp

                            @if($bgMode === 'image' && ! empty($bgImg))
                            <div class="tl-button-wrap">
                                <a href="{{ $url }}" class="tl-button image-mode"
                                    style="{{ $btnStyle }} color: {{ $textColor }};">{{ $label }}</a>
                            </div>
                            @else
                            @php $textColor = $btnTextColor ?? null; @endphp
                            <div class="tl-button-wrap">
                                <a href="{{ $url }}" class="tl-button"
                                    style="{{ $btnStyle }}@if($textColor) color: {{ $textColor }};@endif">{{ $label
                                    }}</a>
                            </div>
                            @endif
                            @elseif($type === 'spacer')
                            <div style="height: {{ intval($section['spacer_height'] ?? 24) }}px;"></div>
                            @endif
                        </div>
                    </section>

                    @endforeach
                </main>

            </div>
        </div>
    </div>
</body>

</html>