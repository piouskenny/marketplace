<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Connection Fee
    |--------------------------------------------------------------------------
    |
    | The fee charged to the connection initiator when the recipient accepts.
    | Stored in the smallest currency unit (kobo for NGN).
    |
    | ₦1,000 = 100,000 kobo
    |
    */

    'connection_fee' => (int) env('MARKETPLACE_CONNECTION_FEE', 100000),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => env('MARKETPLACE_CURRENCY', 'NGN'),

    /*
    |--------------------------------------------------------------------------
    | Platform Name
    |--------------------------------------------------------------------------
    |
    | Neutral placeholder until the client approves a final brand name.
    |
    */

    'name' => env('MARKETPLACE_NAME', 'Skill Link NG'),

];
