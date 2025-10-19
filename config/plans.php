<?php

return [
    'default_plan' => 'solo',
    'plans' => [
        'solo' => [
            'name' => 'Solo Artist',
            // Stripe price code pulled from env; set STRIPE_PRICE_SOLO in .env
            'stripe_price_id' => env('STRIPE_PRICE_SOLO', null),
            'seats' => 4, // 1 owner + 3 sub users
            'features' => [
                '1 User',
                'Includes 3 team accounts. Additional users just $2.99/month each.',
                'Unlimited song & album uploads',
                'Unlimited distribution to streaming platforms',
                'Inventory management tools',
                'Event & task management tools',
                'Comprehensive venue database access',
                'AI mastering tool (Coming Soon)',
                'Various other features',
            ],
            'requires_billing_upfront' => true,
        ],
        'band' => [
            'name' => 'Band',
            'stripe_price_id' => env('STRIPE_PRICE_BAND', null),
            'seats' => 6, // Up to 6 users included; additional billed per user
            'features' => [
                'Up to 6 users',
                'Unlimited song & album uploads',
                'Includes 6 team accounts. Additional users just $2.99/month each.',
                'Unlimited distribution to streaming platforms',
                'Inventory management tools',
                'Event & task management tools',
                'Comprehensive venue database access',
                'AI mastering tool (Coming Soon)',
                'Various other features',
            ],
            'requires_billing_upfront' => true,
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'stripe_price_id' => env('STRIPE_PRICE_ENTERPRISE', null),
            'seats' => 50, // arbitrary higher cap; adjust as needed
            'features' => [
                'Up to 50 users',
                'Priority support',
                'Advanced analytics',
                'Unlimited uploads',
            ],
            'requires_billing_upfront' => true,
        ],
        // custom plan intentionally omitted for now
    ],
];
