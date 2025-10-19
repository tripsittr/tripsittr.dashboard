<?php

use Laravel\Cashier\Console\WebhookCommand;
use Laravel\Cashier\Invoices\DompdfInvoiceRenderer;

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. The "publishable" key is typically used when interacting with
    | Stripe.js while the "secret" key accesses private API endpoints.
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where Cashier's views, such as the payment
    | verification screen, will be available from. You're free to tweak
    | this path according to your preferences and application design.
    |
    */

    'path' => env('CASHIER_PATH', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhooks
    |--------------------------------------------------------------------------
    |
    | Your Stripe webhook secret is used to prevent unauthorized requests to
    | your Stripe webhook handling controllers. The tolerance setting will
    | check the drift between the current time and the signed request's.
    |
    */

    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        'events' => WebhookCommand::DEFAULT_EVENTS,
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Stripe.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Payment Confirmation Notification
    |--------------------------------------------------------------------------
    |
    | If this setting is enabled, Cashier will automatically notify customers
    | whose payments require additional verification. You should listen to
    | Stripe's webhooks in order for this feature to function correctly.
    |
    */

    'payment_notification' => env('CASHIER_PAYMENT_NOTIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | The following options determine how Cashier invoices are converted from
    | HTML into PDFs. You're free to change the options based on the needs
    | of your application or your preferences regarding invoice styling.
    |
    */

    'plans' => [
        // NOTE: Filament Cashier BillingProvider is instantiated with slug 'solo_artist' in AdminPanelProvider.
        // The project already defines STRIPE_PRICE_SOLO / STRIPE_PRICE_BAND / STRIPE_PRICE_ENTERPRISE in .env(.example)
        // but the previous config expected *_PRICE_ID variables that did not exist, causing null price_id and a TypeError.
        // We map and add layered fallbacks so a missing env never results in null.
        'solo_artist' => [
            'product_id' => env('CASHIER_STRIPE_SUBSCRIPTION_PRODUCT_ID'),
            'price_id' => env('SOLO_ARTIST_PRICE_ID',
                env('STRIPE_PRICE_SOLO',
                    // Final fallback to plans config if present
                    (function(){
                        $p = config('plans.plans.solo.stripe_price_id') ?? null;
                        return $p ?: 'missing_solo_price_id';
                    })()
                )
            ),
            'trial_days' => 30,
            'type' => 'default',
            'allow_promotion_codes' => true,
            'collect_tax_ids' => true,
        ],
        'band' => [
            'product_id' => env('CASHIER_STRIPE_SUBSCRIPTION_PRODUCT_ID'),
            'price_id' => env('BAND_PRICE_ID',
                env('STRIPE_PRICE_BAND',
                    (function(){
                        $p = config('plans.plans.band.stripe_price_id') ?? null;
                        return $p ?: 'missing_band_price_id';
                    })()
                )
            ),
            'trial_days' => 30,
            'type' => 'default',
            'allow_promotion_codes' => true,
            'collect_tax_ids' => true,
        ],
        // Mapping organization -> enterprise env variable naming used elsewhere
        'organization' => [
            'product_id' => env('CASHIER_STRIPE_SUBSCRIPTION_PRODUCT_ID'),
            'price_id' => env('ORGANIZATION_PRICE_ID',
                env('STRIPE_PRICE_ENTERPRISE',
                    (function(){
                        $p = config('plans.plans.enterprise.stripe_price_id') ?? null;
                        return $p ?: 'missing_enterprise_price_id';
                    })()
                )
            ),
            'trial_days' => 30,
            'type' => 'default',
            'allow_promotion_codes' => true,
            'collect_tax_ids' => true,
        ],
    ],

    'invoices' => [
        'renderer' => env('CASHIER_INVOICE_RENDERER', DompdfInvoiceRenderer::class),

        'options' => [
            // Supported: 'letter', 'legal', 'A4'
            'paper' => env('CASHIER_PAPER', 'letter'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Logger
    |--------------------------------------------------------------------------
    |
    | This setting defines which logging channel will be used by the Stripe
    | library to write log messages. You are free to specify any of your
    | logging channels listed inside the "logging" configuration file.
    |
    */

    'logger' => env('CASHIER_LOGGER'),

];
