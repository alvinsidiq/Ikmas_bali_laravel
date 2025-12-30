<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'xendit' => [
        'mode' => env('XENDIT_MODE', 'sandbox'),
        'secret_key' => env('XENDIT_SECRET_KEY', env('XENDIT_API_KEY', '')),
        'api_key' => env('XENDIT_API_KEY', env('XENDIT_SECRET_KEY', '')), // backward compatible
        'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
        'checkout_base' => env('XENDIT_CHECKOUT_BASE', 'https://checkout-staging.xendit.co'),
        // Default pattern menggunakan endpoint v4 invoices pay page; override via env jika perlu.
        'checkout_pattern' => env('XENDIT_CHECKOUT_PATTERN', 'https://checkout-staging.xendit.co/v4/invoices/%s/pay'),
        'verify_ssl' => env('XENDIT_VERIFY_SSL', true),
        'mock' => env('XENDIT_MOCK', false),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', null),
    ],

];
