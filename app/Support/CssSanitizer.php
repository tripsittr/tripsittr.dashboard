<?php

namespace App\Support;

class CssSanitizer
{
    /**
     * Whitelisted CSS properties (lowercase)
     * Add/remove properties as needed — keep this list conservative.
     *
     * @var string[]
     */
    protected static array $allowedProperties = [
        'color', 'background', 'background-color', 'background-image', 'background-size', 'background-position',
        'background-repeat', 'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
        'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
        'font-size', 'font-weight', 'font-family', 'line-height', 'text-align', 'text-decoration', 'letter-spacing',
        'border', 'border-radius', 'border-top', 'border-right', 'border-bottom', 'border-left',
        'box-shadow', 'width', 'max-width', 'min-width', 'height', 'max-height', 'min-height',
        'display', 'gap', 'grid-gap', 'grid-template-columns', 'grid-template-rows', 'align-items', 'justify-content',
        'opacity', 'transform', 'overflow', 'white-space'
    ];

    /**
     * Sanitize a raw block of CSS by removing at-rules, comments and any declarations
     * not in the allowed property whitelist. This is intentionally conservative.
     */
    public static function sanitize(?string $css): string
    {
        if (empty($css)) {
            return '';
        }

        // Limit size to avoid very large injections
        $css = mb_strcut($css, 0, 100_000);

        // Remove comments
        $css = preg_replace('!/\*.*?\*/!s', '', $css) ?: '';

        // Remove @-rules entirely (e.g., @import, @font-face, @keyframes, @media)
        $css = preg_replace('/@[^;\{]+;?/', '', $css);
        $css = preg_replace('/@[^\{]+\{[^}]*\}/s', '', $css);

        $out = '';

        // Match selector blocks: selector { declarations }
        if (preg_match_all('/([^\{]+)\{([^}]*)\}/s', $css, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $selector = trim($m[1]);
                $body = $m[2];

                $kept = [];

                // Split declarations by semicolon
                $declarations = preg_split('/;/', $body);
                foreach ($declarations as $decl) {
                    $decl = trim($decl);
                    if ($decl === '') {
                        continue;
                    }

                    // Split property:value
                    $parts = explode(':', $decl, 2);
                    if (count($parts) !== 2) {
                        continue;
                    }
                    $prop = strtolower(trim($parts[0]));
                    $val = trim($parts[1]);

                    // Basic value safety checks
                    $lowerVal = strtolower($val);
                    if (str_contains($lowerVal, 'expression(')
                        || str_contains($lowerVal, 'javascript:')
                        || str_contains($lowerVal, 'vbscript:')
                        || preg_match('/url\s*\(\s*(?:data:|javascript:)/i', $val)
                    ) {
                        continue;
                    }

                    if (in_array($prop, self::$allowedProperties, true)) {
                        $kept[] = $prop.': '.$val.';';
                    }
                }

                if (! empty($kept)) {
                    $out .= $selector.' { '.implode(' ', $kept).' }\n';
                }
            }
        }

        return trim($out);
    }
}
