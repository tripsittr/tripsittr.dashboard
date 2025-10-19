<?php

return [
    // When true, system mail (observer audit-style emails) will not actually send; instead they log.
    'suppress_observer_mail' => env('SUPPRESS_OBSERVER_MAIL', false),
    // Comma separated list of allowed recipient emails in sandbox; non-matching are suppressed.
    'allowed_recipients' => array_filter(array_map('trim', explode(',', env('MAIL_ALLOWED_RECIPIENTS', '')))),
];
