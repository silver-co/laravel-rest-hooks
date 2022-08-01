<?php

return [
    'auth_model' => '',
    'signature_header' => env('REST_HOOKS_SIGNATURE_HEADER', 'X-APPLICATION-HOOK-SIGNATURE'),
    'routes' => [
        'prefix' => 'api',
        'middlewares' => [
            'api',
        ],
    ],
    'batch' => [
        'amount' => env('REST_HOOKS_BATCH_AMOUNT', 500),
        'requests' => env('REST_HOOKS_BATCH_REQUESTS', 10),
    ],
    'schedule' => env('REST_HOOKS_TIME_MINUTES', 1),
];
