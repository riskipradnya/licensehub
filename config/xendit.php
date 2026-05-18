<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Xendit Disbursement Configuration
    |--------------------------------------------------------------------------
    |
    | Secret Key digunakan untuk autentikasi API call ke Xendit.
    | Webhook Token digunakan untuk memverifikasi callback dari Xendit.
    |
    | Sandbox Dashboard: https://dashboard.xendit.co (Test Mode)
    | Production Dashboard: https://dashboard.xendit.co (Live Mode)
    |
    */

    'secret_key'    => env('XENDIT_SECRET_KEY', ''),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | false = Sandbox/Test Mode, true = Production/Live Mode
    |
    */

    'is_production' => env('XENDIT_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | Xendit menggunakan URL yang sama untuk sandbox dan production.
    | Perbedaannya hanya di API Key (test key vs live key).
    |
    */

    'base_url' => 'https://api.xendit.co',
];
