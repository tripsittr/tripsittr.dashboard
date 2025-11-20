<?php

namespace App\Support;

class HtmlSanitizer
{
    /**
     * Very conservative HTML sanitizer: remove <script> tags, on* attributes, and javascript:/data: in URLs.
     */
    public static function sanitizeHtml(?string $html): ?string
    {
        if (empty($html)) {
            return $html;
        }

        // Remove script tags
        $html = preg_replace('#<script[^>]*?>.*?</script>#is', '', $html);

        // Remove event handlers like onclick=, onerror= etc.
        $html = preg_replace('/\son[a-z]+=\"[^\"]*\"/i', '', $html);
        $html = preg_replace("/\son[a-z]+=\\'[^\\']*\\'/i", '', $html);
        $html = preg_replace('/\son[a-z]+=([^\s>]+)/i', '', $html);

        // Remove javascript: and data: schemes from href/src
        $html = preg_replace_callback("/(href|src)\\s*=\\s*(\\\"|\\')?([^\\\"'\\s>]+)(\\\"|\\')?/i", function ($m) {
            $attr = $m[1];
            $val = $m[3] ?? '';
            if (preg_match('/^\s*(javascript:|data:)/i', $val)) {
                return $attr.'="#"';
            }

            return $attr.'="'.htmlspecialchars($val, ENT_QUOTES, 'UTF-8').'"';
        }, $html);

        // Strip comments
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        return $html;
    }

    /**
     * Conservative CSS sanitizer similar to existing CssSanitizer.
     */
    public static function sanitizeCss(?string $css): ?string
    {
        if (empty($css)) {
            return $css;
        }

        // Remove comments
        $css = preg_replace('#/\*.*?\*/#s', '', $css);

        // Remove @-rules (fonts, imports, media) to simplify
        $css = preg_replace('/@[^;{]+[;{][^}]*}/i', '', $css);

        // Remove url(...) with javascript: or data:
        $css = preg_replace_callback('/url\(([^)]+)\)/i', function ($m) {
            $url = trim($m[1], " \"'\t\n\r");
            if (preg_match('/^(javascript:|data:)/i', $url)) {
                return 'url("#")';
            }

            return 'url("'.addslashes($url).'")';
        }, $css);

        return $css;
    }
}
