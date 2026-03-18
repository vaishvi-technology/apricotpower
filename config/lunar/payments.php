<?php

return [

    'default' => env('PAYMENTS_TYPE', 'authorizenet'),

    'types' => [
        'cash-in-hand' => [
            'driver' => 'offline',
            'authorized' => 'payment-offline',
        ],
        'authorizenet' => [
            'driver' => 'authorizenet',
            'authorized' => 'payment-received',
        ],
    ],

];
