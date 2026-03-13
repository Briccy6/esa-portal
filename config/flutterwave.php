<?php

return [
    'secret_key' => env('FLW_SECRET_KEY'),
    'public_key' => env('FLW_PUBLIC_KEY'),
    'encryption_key' => env('FLW_ENCRYPTION_KEY'),
    'webhook_hash' => env('FLUTTERWAVE_WEBHOOK_HASH'),
    'redirect_url' => env('APP_URL') . '/flutterwave/callback',
    'logo_url' => env('APP_URL') . '/images/logo.png',
];
