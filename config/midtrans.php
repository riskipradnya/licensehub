<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Server Key & Client Key dari Midtrans Dashboard.
    | Gunakan sandbox keys untuk testing, production keys untuk live.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Set to true for production, false for sandbox/testing.
    |
    */

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Sanitization & 3DS
    |--------------------------------------------------------------------------
    */

    'is_sanitized' => true,
    'is_3ds' => true,
];
